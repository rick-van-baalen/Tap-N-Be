<?php

class OrdersBC extends BusinessComponent {

    public function hasActiveOrder() {
        $this->query("SELECT ID FROM orders WHERE SESSION_ID = ?");
        $this->bind(1, $_SESSION['SESSION_ID']);
        $this->execute();
        
        return $this->rowCount() > 0 ? true : false;
    }

    public function getOrderByHashedID($hashed_order_id) {
        $this->query("SELECT * FROM orders WHERE HASHED_ID = ?");
        $this->bind(1, $hashed_order_id);
        $this->execute();

        return $this->getResult();
    }

    public function getOrderByHashedPaymentID($hashed_payment_id) {
        $this->query("SELECT * FROM payments WHERE HASHED_ID = ?");
        $this->bind(1, $hashed_payment_id);
        $this->execute();
        $payment = $this->getResult();
        if ($payment == "" || $payment->ORDER_ID == "") return false;

        $this->query("SELECT * FROM orders WHERE ID = ?");
        $this->bind(1, $payment->ORDER_ID);
        $this->execute();

        return $this->getResult();
    }

    public function getOrderLines($order_id) {
        try {
            $this->query("SELECT orderlines.ID, orderlines.PRODUCT_ID, orderlines.AMOUNT, orderlines.PRICE, products.DESCRIPTION, products.IMAGE FROM orderlines LEFT JOIN products ON orderlines.PRODUCT_ID = products.ID WHERE orderlines.ORDER_ID = ?");
            $this->bind(1, $order_id);
            $this->execute();
            
            return $this->getResults();
        } catch(PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function createOrder() {
        try {
            $this->query("INSERT INTO orders (SESSION_ID, STATUS, TABLE_ID) VALUES (?, 'Open', ?)");
            $this->bind(1, $_SESSION['SESSION_ID']);
            $this->bind(2, $_SESSION['TABLE_ID']);
            $this->execute();
            $order_id = $this->getInsertID();

            $hashed_id = md5($order_id);

            $this->query("UPDATE orders SET HASHED_ID = ? WHERE ID = ?");
            $this->bind(1, $hashed_id);
            $this->bind(2, $order_id);
            $this->execute();

            $_SESSION['ORDER_ID'] = $order_id;
            $_SESSION['HASHED_ORDER_ID'] = $hashed_id;
        } catch(PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function addToOrder($product_id) {
        $this->query("SELECT STATUS FROM orders WHERE ID = ?");
        $this->bind(1, $_SESSION['ORDER_ID']);
        $this->execute();
        $order = $this->getResult();
        if ($order != "" && $order->STATUS != "Open") return false;

        $this->query("SELECT PRICE FROM products WHERE ID = ?");
        $this->bind(1, $product_id);
        $this->execute();
        $product = $this->getResult();

        $this->query("SELECT ID FROM orderlines WHERE ORDER_ID = ? AND PRODUCT_ID = ?");
        $this->bind(1, $_SESSION['ORDER_ID']);
        $this->bind(2, $product_id);
        $this->execute();

        if ($this->rowCount() > 0) {
            // UPDATE
            try {
                $this->query("UPDATE orderlines SET AMOUNT = AMOUNT + 1 WHERE ORDER_ID = ? AND PRODUCT_ID = ?");
                $this->bind(1, $_SESSION['ORDER_ID']);
                $this->bind(2, $product_id);
                return $this->execute();
            } catch(PDOException $e) {
                return false;
            }
        } else {
            // INSERT
            try {
                $this->query("INSERT INTO orderlines (ORDER_ID, PRODUCT_ID, AMOUNT, PRICE) VALUES (?, ?, 1, ?)");
                $this->bind(1, $_SESSION['ORDER_ID']);
                $this->bind(2, $product_id);
                $this->bind(3, $product->PRICE);
                return $this->execute();
            } catch(PDOException $e) {
                return false;
            }
        }
    }

    public function removeFromOrder($product_id) {
        $this->query("SELECT STATUS FROM orders WHERE ID = ?");
        $this->bind(1, $_SESSION['ORDER_ID']);
        $this->execute();
        $order = $this->getResult();
        if ($order != "" && $order->STATUS != "Open") return false;

        try {
            $this->query("UPDATE orderlines SET AMOUNT = AMOUNT - 1 WHERE PRODUCT_ID = ?");
            $this->bind(1, $product_id);
            return $this->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function removeOrderLine($orderline_id) {
        $this->query("SELECT STATUS FROM orders WHERE ID = ?");
        $this->bind(1, $_SESSION['ORDER_ID']);
        $this->execute();
        $order = $this->getResult();
        if ($order != "" && $order->STATUS != "Open") return false;

        try {
            $this->query("DELETE FROM orderlines WHERE ID = ?");
            $this->bind(1, $orderline_id);
            return $this->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getTotalOrderAmount($order_id) {
        if ($this->hasActiveOrder()) {
            $orderlines = $this->getOrderLines($order_id);

            $amount = 0;
            foreach ($orderlines as $orderline) {
                $amount += $orderline->AMOUNT;
            }

            return $amount;
        } else {
            return 0;
        }
    }

    public function getTotalOrderPrice($order_id) {
        $orderlines = $this->getOrderLines($order_id);

        $totalPrice = 0;
        foreach ($orderlines as $orderline) {
            $totalPrice += $orderline->PRICE * $orderline->AMOUNT;
        }

        return $totalPrice;
    }

    public function getOrderFromOrderLine($orderline_id) {
        try {
            $this->query("SELECT ORDER_ID FROM orderlines WHERE ID = ?");
            $this->bind(1, $orderline_id);
            $this->execute();
            return $this->getResult();
        } catch(PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function clearOrder() {
        $this->query("SELECT STATUS FROM orders WHERE ID = ?");
        $this->bind(1, $_SESSION['ORDER_ID']);
        $this->execute();
        $order = $this->getResult();
        if ($order != "" && $order->STATUS != "Open") return false;
        
        try {
            $this->query("DELETE FROM orderlines WHERE ORDER_ID = ?");
            $this->bind(1, $_SESSION['ORDER_ID']);
            return $this->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getTableNumber($order_id) {
        try {
            $this->query("SELECT sessions.HASHED_TABLE_ID FROM orders LEFT JOIN sessions ON orders.SESSION_ID = sessions.ID WHERE orders.ID = ?");
            $this->bind(1, $order_id);
            $this->execute();
            $session = $this->getResult();
            if ($session->HASHED_TABLE_ID === null) return false;

            $this->query("SELECT tables.TABLE_NUMBER FROM tables WHERE tables.HASHED_ID = ?");
            $this->bind(1, $session->HASHED_TABLE_ID);
            $this->execute();
            $table = $this->getResult();
            if ($table->TABLE_NUMBER === null) return false;

            return $table->TABLE_NUMBER;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getLastModified($order_id) {
        try {
            $this->query("SELECT LAST_MODIFIED FROM orderlines WHERE ORDER_ID = ? ORDER BY LAST_MODIFIED DESC LIMIT 1");
            $this->bind(1, $order_id);
            $this->execute();

            $orderline = $this->getResult();

            if ($orderline->LAST_MODIFIED != "") {
                $last_modified = date("d-m-Y H:i:s", strtotime($orderline->LAST_MODIFIED));
                return $last_modified;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            return false;
        }
    }

    public function changeOrderStatus($order_id, $status) {
        try {
            $this->query("UPDATE orders SET STATUS = ? WHERE ID = ?");
            $this->bind(1, $status);
            $this->bind(2, $order_id);
            return $this->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getOrderStatus($order_id) {
        try {
            $this->query("SELECT STATUS FROM orders WHERE ID = ?");
            $this->bind(1, $order_id);
            $this->execute();
            $order = $this->getResult();

            if ($order != "") {
                return $order->STATUS;
            } else {
                return "";
            }
        } catch(PDOException $e) {
            return false;
        }
    }

    public function cancelOrder($order_id) {
        try {
            $this->query("UPDATE orders SET STATUS = 'Canceled' WHERE ID = ?");
            $this->bind(1, $order_id);
            return $this->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function completeOrder($order_id) {
        try {
            $this->query("UPDATE orders SET STATUS = 'Completed' WHERE ID = ?");
            $this->bind(1, $order_id);
            return $this->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function checkOpenStatus() {
        try {
            $this->query("SELECT STATUS FROM orders WHERE ID = ?");
            $this->bind(1, $_SESSION['ORDER_ID']);
            $this->execute();
            $order = $this->getResult();

            if ($order != "" && $order->STATUS == "Open") {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateOrder($order_id, $customer_id, $table_number) {
        try {
            $this->query("SELECT * FROM tables WHERE TABLE_NUMBER = ?");
            $this->bind(1, $table_number);
            $this->execute();
            $table = $this->getResult();
            if ($table == "" || $table->ID == "") return false;

            $this->query("UPDATE orders SET CUSTOMER_ID = ?, TABLE_ID = ? WHERE ID = ?");
            $this->bind(1, $customer_id);
            $this->bind(2, $table->ID);
            $this->bind(3, $order_id);
            return $this->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

}