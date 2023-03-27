<?php

require_once APP_ROOT . '/models/OrdersBC.php';
require_once APP_ROOT . '/models/CustomersBC.php';
require_once APP_ROOT . '/models/PaymentsBC.php';
require_once APP_ROOT . '/models/TablesBC.php';
require_once APP_ROOT . '/libraries/Mail.php';

class Checkout extends Controller {

    private $data;

    public function index($hashed_payment_id = null) {
        $OrdersBC = new OrdersBC();

        $this->data['title'] = 'Bestellen';
        $this->data['description'] = 'Bestellen';
        $this->data['keywords'] = 'Bestellen';

        if ($hashed_payment_id != null) {
            $PaymentsBC = new PaymentsBC();
            $payment = $PaymentsBC->getPaymentByHashedID($hashed_payment_id);
            if ($payment === false) die("Unknown payment.");

            $this->data['hashed_payment_id'] = $hashed_payment_id;

            switch ($payment->STATUS) {
                case "open":
                    $this->view('CheckoutOpen', $this->data);
                    break;
                case "paid":
                    $OrdersBC->createOrder();

                    $order = $OrdersBC->getOrderByHashedPaymentID($hashed_payment_id);
                    if ($order === false) die("Onbekende order.");
                    $this->data['order'] = $order;

                    $CustomersBC = new CustomersBC();
                    $customer = $CustomersBC->getCustomer($order->CUSTOMER_ID);
                    $this->data['customer'] = $customer;
                    
                    $orderlines = $OrdersBC->getOrderLines($order->ID);
                    $this->data['orderlines'] = $orderlines;

                    $total_price = $OrdersBC->getTotalOrderPrice($order->ID);
                    $this->data['total_price'] = $total_price;

                    $TablesBC = new TablesBC();
                    $table = $TablesBC->getTable($order->TABLE_ID);
                    $this->data['table'] = $table;

                    $this->view('CheckoutSuccess', $this->data);
                    break;
                case "failed":
                case "expired":
                case "canceled":
                    $this->view('CheckoutFailed', $this->data);
                    break;
            }
        } else {
            $orderlines = $OrdersBC->getOrderLines($_SESSION['ORDER_ID']);
            $totalPrice = $OrdersBC->getTotalOrderPrice($_SESSION['ORDER_ID']);
            $table_number = $OrdersBC->getTableNumber($_SESSION['ORDER_ID']);
            
            $this->data['total_price'] = $totalPrice;
            $this->data['orderlines'] = $orderlines;
            if ($table_number !== false) $this->data['table_number'] = $table_number;

            $this->view('Checkout', $this->data);
        }
    }

    public function tableExists($table_number) {
        $TablesBC = new TablesBC();
        $table = $TablesBC->getTableByTableNumber($table_number);

        if ($table != "" && $table->ID != "") {
            echo true; // For AJAX request.
        } else {
            echo false; // For AJAX request.
        }
    }

    public function startPayment() {
        $first_name = isset($_POST['first_name']) ? htmlentities($_POST['first_name']) : null;
        $last_name = isset($_POST['last_name']) ? htmlentities($_POST['last_name']) : null;
        $email = isset($_POST['email']) ? htmlentities($_POST['email']) : null;
        $table_number = isset($_POST['table_number']) ? htmlentities($_POST['table_number']) : null;
        if ($first_name === null || $last_name === null || $email === null || $table_number === null) return;

        $_SESSION['CUSTOMER_FIRST_NAME'] = $first_name;
        $_SESSION['CUSTOMER_LAST_NAME'] = $last_name;
        $_SESSION['CUSTOMER_EMAIL'] = $email;
        $_SESSION['CUSTOMER_TABLE'] = $table_number;

        // 1. Create customer
        $CustomersBC = new CustomersBC();
        $customer_id = $CustomersBC->addCustomer($first_name, $last_name, $email);
        
        if ($customer_id !== false) {
            // 2. Link Order to Customer
            $OrdersBC = new OrdersBC();
            $result = $OrdersBC->updateOrder($_SESSION['ORDER_ID'], $customer_id, $table_number);
            if ($result !== false) {
                $PaymentsBC = new PaymentsBC();
                $total_order_price = $OrdersBC->getTotalOrderPrice($_SESSION['ORDER_ID']);
                
                // 3. Create payment in database
                $hashed_payment_id = $PaymentsBC->addPayment($_SESSION['ORDER_ID'], $total_order_price);
                if ($hashed_payment_id !== false) {
                    // 4. Register payment with payment provider
                    switch (MODULE_PAYMENT_PROVIDER) {
                        case "Mollie":
                            require_once APP_ROOT . '/models/MollieBC.php';
                            $MollieBC = new MollieBC();

                            $json = $MollieBC->createPayment($hashed_payment_id, $_SESSION['ORDER_ID'], $total_order_price);
                            if ($json !== false) {
                                $payment_id = $json['id'];
                                $payment_link = $json['_links']['checkout']['href'];
                            } else {
                                die("Error while creating Mollie payment.");
                            }

                            break;
                        default:
                            die("Error: '" . MODULE_PAYMENT_PROVIDER . "' is not a valid payment provider. Check Config.php.");
                            break;
                    }

                    // 5. Update payment in database
                    $result = $PaymentsBC->updatePayment($hashed_payment_id, $payment_id, $payment_link);
                    if ($result !== false) {
                        // 6. Send customer to payment link
                        header("Location: " . $payment_link);
                        exit();
                    }
                }
            }
        }
    }

    public function webhook() {
        if (!file_exists(APP_ROOT . "/logs")) mkdir(APP_ROOT . "/logs", 0777, true);
        $webhook_log = fopen(APP_ROOT . "/logs/webhook_log.txt", "a");

        $data = file_get_contents("php://input");
        fwrite($webhook_log, date('Y-m-d H:i:s') . " Data received: " . $data . "\n");

        $error = true;
        $update_order = false;

        switch (MODULE_PAYMENT_PROVIDER) {
            case "Mollie":
                // Data from Mollie: id=tr_tn4WMbPduw
                if (strpos($data, 'id=') !== false) {
                    $payment_id = str_replace("id=", "", $data);

                    require_once APP_ROOT . '/models/MollieBC.php';
                    $MollieBC = new MollieBC();
                    $response = $MollieBC->getPayment($payment_id);
                    fwrite($webhook_log, date('Y-m-d H:i:s') . " Response from Mollie: " . $response . "\n");

                    $json = json_decode($response, true);
                    if (isset($json['status'])) {
                        $status = $json['status'];
                        if ($status == "paid") $update_order = true;

                        $order_id = $json['metadata'];    
                        $order_id = str_replace("order_id=", "", $order_id);

                        $error = false;
                    } else {
                        fwrite($webhook_log, date('Y-m-d H:i:s') . " Something went wrong while getting PAYMENT_ID " . $payment_id . " from the Mollie API.\n");
                    }
                }

                break;
            default:
                fwrite($webhook_log, date('Y-m-d H:i:s') . " '" . MODULE_PAYMENT_PROVIDER . "' is not a valid payment provider. Check Config.php.\n");
                break;
        }

        if ($error === false) {
            $PaymentsBC = new PaymentsBC();
            $result = $PaymentsBC->changeStatus($payment_id, $status);
            if ($result !== false) {
                fwrite($webhook_log, date('Y-m-d H:i:s') . " Status for PAYMENT_ID " . $payment_id . " changed to " . $status . ".\n");

                if ($update_order) {
                    $OrdersBC = new OrdersBC();
                    $result = $OrdersBC->changeOrderStatus($order_id, "Completed");
                    if ($result) {
                        fwrite($webhook_log, date('Y-m-d H:i:s') . " Status for ORDER_ID " . $order_id . " changed to 'Completed'.\n");
                        // $result = Mail::sendOrderConfirmation($order_id);
                        // if ($result === false) {
                        //     fwrite($webhook_log, date('Y-m-d H:i:s') . " Something went wrong while sending the customer an e-mail for ORDER_ID " . $order_id . ".\n");
                        // } else {
                        //     fwrite($webhook_log, date('Y-m-d H:i:s') . " Confirmation e-mail send to the customer for ORDER_ID " . $order_id . ".\n");
                        // }
                    } else {
                        fwrite($webhook_log, date('Y-m-d H:i:s') . " Something went wrong while changing the order status for ORDER_ID " . $order_id . " to 'Completed'.\n");
                    }
                }
            } else {
                fwrite($webhook_log, date('Y-m-d H:i:s') . " Something went wrong while changing the status for PAYMENT_ID " . $payment_id . " to " . $status . ".\n");
            }
        } else {
            fwrite($webhook_log, date('Y-m-d H:i:s') . " Something went wrong while creating the payment.\n");
        }

        fclose($webhook_log);
    }

    public function checkOpenPaymentStatus($hashed_payment_id) {
        $PaymentsBC = new PaymentsBC();
        echo $PaymentsBC->checkOpenPaymentStatus($hashed_payment_id);
    }

}