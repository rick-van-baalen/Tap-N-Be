<?php

class TablesBC extends BusinessComponent {

    public function getTables() {
        $this->query("SELECT * FROM tables");
        $this->execute();
        return $this->getResults();
    }

    public function getTable($table_id) {
        try {
            $this->query("SELECT * FROM tables WHERE ID = ?");
            $this->bind(1, $table_id);
            $this->execute();
            
            if ($this->rowCount() == 0) {
                return false;
            } else {
                return $this->getResult();
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getTableByHashedID($hashed_table_id) {
        try {
            $this->query("SELECT * FROM tables WHERE HASHED_ID = ?");
            $this->bind(1, $hashed_table_id);
            $this->execute();
            
            if ($this->rowCount() == 0) {
                return false;
            } else {
                return $this->getResult();
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getTableByTableNumber($table_number) {
        try {
            $this->query("SELECT * FROM tables WHERE TABLE_NUMBER = ?");
            $this->bind(1, $table_number);
            $this->execute();
            
            if ($this->rowCount() == 0) {
                return false;
            } else {
                return $this->getResult();
            }
        } catch (PDOException $e) {
            return false;
        }
    }

}