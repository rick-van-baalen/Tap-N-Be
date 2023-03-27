<?php
require_once 'libraries/Core.php';
require_once 'libraries/Controller.php';
require_once 'libraries/BusinessComponent.php';
require_once 'config/Config.php';
require_once 'models/SessionsBC.php';

session_start();

$SessionsBC = new SessionsBC();
if ($SessionsBC->hasActiveSession() === false) {
    $SessionsBC->getSession();

    $_SESSION['show_instruction'] = true;

    if (strpos($_SERVER['QUERY_STRING'], "Order/") !== false) {
        $hashed_order_id = str_replace("Order/", "", $_SERVER['QUERY_STRING']);
        if ($hashed_order_id != "") $_SESSION['show_instruction'] = false;
    }
}

$init = new Core();