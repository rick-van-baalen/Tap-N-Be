<?php

class CustomersBC extends BusinessComponent {

    public function getCustomer($customer_id) {
        $this->query("SELECT * FROM customers WHERE ID = ?");
        $this->bind(1, $customer_id);
        $this->execute();

        return $this->getResult();
    }

    public function getCustomerFromOrder($order_id) {
        $this->query("SELECT orders.CUSTOMER_ID, customers.FIRST_NAME, customers.LAST_NAME, customers.EMAIL FROM orders LEFT JOIN customers ON orders.CUSTOMER_ID = customers.ID WHERE orders.ID = ?");
        $this->bind(1, $order_id);
        $this->execute();

        return $this->getResult();
    }

    public function addCustomer($first_name, $last_name, $email) {
        try {
            $this->query("INSERT INTO customers (FIRST_NAME, LAST_NAME, EMAIL) VALUES (?, ?, ?)");
            $this->bind(1, $first_name);
            $this->bind(2, $last_name);
            $this->bind(3, $email);
            $this->execute();

            return $this->getInsertID();
        } catch (PDOException $e) {
            return false;
        }
    }

}