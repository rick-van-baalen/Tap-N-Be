<?php

require_once APP_ROOT . '/models/OrdersBC.php';

class Order extends Controller {

    private $data;

    public function index($hashed_order_id = null) {
        $OrdersBC = new OrdersBC();

        if ($hashed_order_id != null) {
            // View for the person who scans the QR code.
            $this->data['is_customer'] = false;
            
            $this->data['title'] = 'Bestellijstje';
            $this->data['description'] = 'Bestellijstje';
            $this->data['keywords'] = 'Bestellijstje';
            $this->data['status'] = "";

            $order = $OrdersBC->getOrderByHashedID($hashed_order_id);
            if ($order !== false) {
                $this->data['order_id'] = $_SESSION['ORDER_ID'];
                $this->data['status'] = $order->STATUS;

                // Change order status to "Confirmed".
                if ($OrdersBC->getOrderStatus($order->ID) == "Open") {
                    $OrdersBC->changeOrderStatus($order->ID, "Confirmed");
                }

                $orderlines = $OrdersBC->getOrderLines($order->ID);
                $this->data['orderlines'] = $orderlines;

                $totalPrice = $OrdersBC->getTotalOrderPrice($order->ID);
                $this->data['total_price'] = $totalPrice;
                
                $table_number = $OrdersBC->getTableNumber($order->ID);
                if ($table_number !== false) $this->data['table_number'] = $table_number;

                $last_modified = $OrdersBC->getLastModified($order->ID);
                if ($last_modified !== false) $this->data['last_modified'] = $last_modified;
            }

            $this->data['show_header_icons'] = false;

            if ($this->data['status'] == "Canceled") {
                $this->view('OrderCanceled', $this->data);
            } else if ($this->data['status'] == "Completed") {
                $this->view('OrderCompleted', $this->data);
            } else {
                $this->view('OrderSummary', $this->data);
            }
        } else {
            // View for the customer's order.
            $this->data['is_customer'] = true;

            if ($OrdersBC->hasActiveOrder() && $_SESSION['ORDER_ID'] != "") {
                $orderlines = $OrdersBC->getOrderLines($_SESSION['ORDER_ID']);
                $totalPrice = $OrdersBC->getTotalOrderPrice($_SESSION['ORDER_ID']);
                $status = $OrdersBC->getOrderStatus($_SESSION['ORDER_ID']);
            } else {
                $orderlines = [];
                $totalPrice = 0;
                $status = "";
            }

            $this->data['title'] = 'Bestellijstje';
            $this->data['description'] = "Bekijk je bestellijstje op " . SITE_NAME . ".";
            $this->data['keywords'] = 'Bestellijstje, Bestellen';
            $this->data['total_price'] = $totalPrice;
            $this->data['orderlines'] = $orderlines;
            $this->data['status'] = $status;

            if ($this->data['status'] == "Confirmed") {
                $this->view('OrderConfirmed', $this->data);
            } else if ($this->data['status'] == "Canceled") {
                $this->view('OrderCanceled', $this->data);
            } else if ($this->data['status'] == "Completed") {
                $this->view('OrderCompleted', $this->data);
            } else {
                $this->view('Order', $this->data);
            }
        }
    }

    public function updateCart() {
        $OrdersBC = new OrdersBC();
        if ($OrdersBC->hasActiveOrder()) {
            $orderlines = $OrdersBC->getOrderLines($_SESSION['ORDER_ID']);
        } else {
            $orderlines = [];
        }

        $totalPrice = $OrdersBC->getTotalOrderPrice($_SESSION['ORDER_ID']);

        $data = [
            'total_price' => $totalPrice,
            'orderlines' => $orderlines
        ];

        $this->view('OrderLines', $data);
    }

    public function addToOrder() {
        $productid = isset($_GET['productid']) ? $_GET['productid'] : "";
        if ($productid == "") return;

        $OrdersBC = new OrdersBC();
        if ($OrdersBC->hasActiveOrder()) {
            $result = $OrdersBC->addToOrder($productid);
        } else {
            $OrdersBC->createOrder();
            $result = $OrdersBC->addToOrder($productid);
        }
        
        $alert = $result === false ? "danger" : "success";

        $show_link = strpos($_SERVER['HTTP_REFERER'], '/Order') === false ? true : false;

        $data = [
            'type' => $alert,
            'success_message' => 'Product is toegevoegd aan je bestellijstje.',
            'danger_message' => 'Er ging iets fout. Product is niet toegevoegd aan je bestellijstje.',
            'icon' => 'clipboard',
            'show_link' => $show_link,
            'link_href' => URL_ROOT . '/Order/',
            'link_text' => 'Bekijk bestellijstje'
        ];

        $this->view('Alert', $data);
    }

    public function decreaseAmount() {
        $productid = isset($_GET['productid']) ? $_GET['productid'] : "";
        if ($productid == "") return;

        $OrdersBC = new OrdersBC();
        if ($OrdersBC->hasActiveOrder()) {
            $result = $OrdersBC->removeFromOrder($productid);
        }
        
        $alert = $result === false ? "danger" : "success";

        $data = [
            'type' => $alert,
            'success_message' => 'Je bestellijstje is aangepast.',
            'danger_message' => 'Er ging iets fout. Je bestellijstje is niet aangepast.',
            'icon' => 'clipboard'
        ];

        $this->view('Alert', $data);
    }

    public function removeOrderLine() {
        $orderlineid = isset($_GET['orderlineid']) ? $_GET['orderlineid'] : "";
        if ($orderlineid == "") return;

        $OrdersBC = new OrdersBC();
        if ($OrdersBC->hasActiveOrder()) {
            $result = $OrdersBC->removeOrderLine($orderlineid);
        }
        
        $alert = $result === false ? "danger" : "success";

        $data = [
            'type' => $alert,
            'success_message' => 'Je bestellijstje is aangepast.',
            'danger_message' => 'Er ging iets fout. Je bestellijstje is niet aangepast.',
            'icon' => 'clipboard'
        ];

        $this->view('Alert', $data);
    }

    public function getTotalOrderPrice() {
        $OrdersBC = new OrdersBC();
        if ($OrdersBC->hasActiveOrder()) {
            echo $OrdersBC->getTotalOrderPrice($_SESSION['ORDER_ID']);
        } else {
            echo 0;
        }
    }

    public function clearOrder() {
        $OrdersBC = new OrdersBC();
        if ($OrdersBC->hasActiveOrder()) {
            $OrdersBC->clearOrder();
        }
    }

    public function createQR() {
        include_once APP_ROOT . '/libraries/QR.php';

        $timestamp = str_replace(":", "_", date('Y-m-d h:i:s', time()));
        $timestamp = str_replace(" ", "_", $timestamp);
        $timestamp = str_replace("-", "_", $timestamp);

        $fileName = "QR_" . $_SESSION['ORDER_ID'] . "_" . $timestamp . ".png";
        $path = "temp/" . $fileName;
        $url = URL_ROOT . "/Order/" . $_SESSION['HASHED_ORDER_ID'];

        if (!is_dir("temp/")) mkdir("temp/");

        QRcode::png($url, $path, QR_ECLEVEL_L, 10, 1);

        $data = [
            'URL' => $url,
            'PATH' => URL_ROOT . '/' . $path
        ];
        
        $this->view('QR', $data);
    }

    public function cancelOrder() {
        $_SESSION['ORDER_ID'] = isset($_GET['order_id']) ? $_GET['order_id'] : null;
        if ($_SESSION['ORDER_ID'] === null) return;

        $OrdersBC = new OrdersBC();
        $OrdersBC->cancelOrder($_SESSION['ORDER_ID']);
    }

    public function completeOrder() {
        $_SESSION['ORDER_ID'] = isset($_GET['order_id']) ? $_GET['order_id'] : null;
        if ($_SESSION['ORDER_ID'] === null) return;

        $OrdersBC = new OrdersBC();
        $OrdersBC->completeOrder($_SESSION['ORDER_ID']);
    }

    public function createNewOrder() {
        $OrdersBC = new OrdersBC();
        $OrdersBC->createOrder();
    }

    public function checkOpenStatus() {
        $OrdersBC = new OrdersBC();
        echo $OrdersBC->checkOpenStatus();
    }

}