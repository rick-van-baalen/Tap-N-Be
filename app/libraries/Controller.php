<?php

class Controller {

    public function model($model) {
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
        } else {
            die('There is no model found for: ' . $model);
        }
        return new $model();
    }

    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('There is no view found for: ' . $view);
        }
    }

    public function getParameters() {
        $url = $_SERVER['REQUEST_URI'];
        if (strpos($url, '?') > 0) {
            $parameters = substr($url, strpos($url, '?') + 1, strlen($url));
            $parameters = explode('&', $parameters);
            return $parameters;
        } else {
            return array();
        }
    }

}