<?php

abstract class BusinessComponent {

    private $statement;
    private $handler;

    public function __construct() {
        $connection = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->handler = new PDO($connection, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function query($sql) {
        $this->statement = $this->handler->prepare($sql);
    }

    public function bind($parameter, $value, $type = null) {
        switch(is_null($type)) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
        }

        $this->statement->bindValue($parameter, $value, $type);
    }

    public function execute() {
        return $this->statement->execute();
    }

    public function getResults() {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function getResult() {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount() {
        return $this->statement->rowCount();
    }

    public function getInsertID() {
        return $this->handler->lastInsertId();
    }

}