<?php require_once APP_ROOT . '/models/OrdersBC.php';
$OrdersBC = new OrdersBC(); ?>

<!DOCTYPE html>
    <head>
        <title><?php echo $data['title']; ?> | <?php echo SITE_NAME; ?></title>
        <meta charset="UTF-8">
        <meta name="description" content="<?php echo $data['description']; ?>">
        <meta name="keywords" content="<?php echo $data['keywords']; ?>">
        <meta name="author" content="<?php echo SITE_NAME; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT; ?>/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT; ?>/css/style.css">
        <script src="<?php echo URL_ROOT; ?>/js/Config.js"></script>
        <script src="<?php echo URL_ROOT; ?>/js/Livesearch.js"></script>
        <script src="<?php echo URL_ROOT; ?>/js/Order.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    </head>
    <body id="body">
        <header class="header">
            <div class="container">
                <div class="row">
                    <div class="col-6 d-flex align-items-center justify-content-start">
                        <a href="<?php echo URL_ROOT; ?>" class="mb-0 logo">Tap 'n Be</a>
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end icons">
                        <?php if (!isset($data['show_header_icons']) || $data['show_header_icons'] === true) { ?>
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#search"><img class="search" src="<?php echo URL_ROOT; ?>/images/icons/search.svg"></a>
                        <?php if (MODULE_QR || MODULE_PAYMENT) { ?>
                        <a href="<?php echo URL_ROOT; ?>/Order">
                            <div class="order">
                                <img class="list" src="<?php echo URL_ROOT; ?>/images/icons/clipboard.svg">
                                <span id="order-count" class="order-count"><?php echo $OrdersBC->getTotalOrderAmount($_SESSION['ORDER_ID']); ?></span>
                            </div>
                        </a>
                        <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="search" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Waar ben je naar op zoek?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" onkeyup="showResult(this.value)" class="form-control" id="search" name="search" value="" placeholder="Begin met typen...">
                            <div id="search-results"></div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div id="loader" class="loader"></div>
        <div id="alert"></div>

        <div class="container">