<?php

require_once APP_ROOT . '/models/TablesBC.php';

class SessionsBC extends BusinessComponent {

    public function hasActiveSession() {
        if (!isset($_SESSION['SESSION_ID']) || $_SESSION['SESSION_ID'] == "") return false;

        $this->query("SELECT * FROM sessions WHERE ID = ?");
        $this->bind(1, $_SESSION['SESSION_ID']);
        $this->execute();

        return $this->rowCount() > 0;
    }

    public function getSession() {
        $id = uniqid("ses", true);
        $hashed_table_id = isset($_GET['tafel']) && $_GET['tafel'] != "" ? $_GET['tafel'] : null;

        if ($hashed_table_id != null) {
            $TablesBC = new TablesBC();
            $table = $TablesBC->getTableByHashedID($hashed_table_id);
            
            if ($table !== false) {
                $_SESSION['TABLE_ID'] = $table->ID;
                $_SESSION['HASHED_TABLE_ID'] = $hashed_table_id;
            } else  {
                $_SESSION['TABLE_ID'] = null;
                $_SESSION['HASHED_TABLE_ID'] = null;
            }
        } else {
            $_SESSION['TABLE_ID'] = null;
            $_SESSION['HASHED_TABLE_ID'] = null;
        }

        $this->query("INSERT INTO sessions (ID, HASHED_TABLE_ID) VALUES (?, ?)");
        $this->bind(1, $id);
        $this->bind(2, $hashed_table_id);
        $this->execute();

        $_SESSION['SESSION_ID'] = $id;
        $_SESSION['ORDER_ID'] = "";
        $_SESSION['HASHED_ORDER_ID'] = "";
    }

}