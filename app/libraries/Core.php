<?php

class Core {
    protected $currentController = 'PageNotFound'; // If no controller is found, PageNotFound should be used.
    protected $currentMethod = 'index'; // If no method is found, the index method should be used.
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        if (file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
        }

        require_once '../app/controllers/' . $this->currentController . '.php';
        $this->currentController = new $this->currentController;
        
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];
        
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            return $url;
        } else {
            // If no page name is found, the menu should be displayed as default.
            $url = array();
            $url[0] = "Menu";

            return $url;
        }
    }

}