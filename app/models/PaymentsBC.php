<?php

class PaymentsBC extends BusinessComponent {

    public function getPaymentByHashedID($hashed_payment_id) {
        $this->query("SELECT * FROM payments WHERE HASHED_ID = ?");
        $this->bind(1, $hashed_payment_id);
        $this->execute();

        return $this->getResult();
    }

    public function addPayment($order_id, $amount) {
        try {
            $this->query("INSERT INTO payments (ORDER_ID, AMOUNT, STATUS) VALUES (?, ?, 'open')");
            $this->bind(1, $order_id);
            $this->bind(2, $amount);
            $this->execute();

            $payment_id = $this->getInsertID();
            $hashed_id = md5($payment_id);

            $this->query("UPDATE payments SET HASHED_ID = ? WHERE ID = ?");
            $this->bind(1, $hashed_id);
            $this->bind(2, $payment_id);
            $this->execute();

            return $hashed_id;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updatePayment($hashed_payment_id, $payment_id, $payment_link) {
        try {
            $this->query("UPDATE payments SET PAYMENT_ID = ?, PAYMENT_LINK = ? WHERE HASHED_ID = ?");
            $this->bind(1, $payment_id);
            $this->bind(2, $payment_link);
            $this->bind(3, $hashed_payment_id);

            return $this->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function changeStatus($payment_id, $status) {
        try {
            $this->query("UPDATE payments SET STATUS = ? WHERE PAYMENT_ID = ?");
            $this->bind(1, $status);
            $this->bind(2, $payment_id);
            return $this->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function checkOpenPaymentStatus($hashed_payment_id) {
        try {
            $this->query("SELECT STATUS FROM payments WHERE HASHED_ID = ?");
            $this->bind(1, $hashed_payment_id);
            $this->execute();
            $payment = $this->getResult();

            if ($payment != "" && $payment->STATUS == "open") {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            return false;
        }
    }

}