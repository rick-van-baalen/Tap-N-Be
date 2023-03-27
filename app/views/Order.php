<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<h2 class="text-center mb-4">Jouw bestellijstje</h2>

<section id="order" class="mb-5">
    <?php include 'OrderLines.php'; ?>
</section>

<section class="mb-5">
    <div class="row">
        <div class="col-6 d-flex align-items-center justify-content-start">
            <?php if (isset($data['breadcrumb'])) { ?>
            <a class="btn btn-back" href="<?php echo $data['breadcrumb']; ?>"><img src="<?php echo URL_ROOT; ?>/images/icons/arrow-left.svg"> Terug</a>
            <?php } else if (isset($_SERVER['HTTP_REFERER'])) { ?>
            <a class="btn btn-back" href="<?php echo $_SERVER['HTTP_REFERER']; ?>"><img src="<?php echo URL_ROOT; ?>/images/icons/arrow-left.svg"> Terug</a>
            <?php } ?>
        </div>
        <div class="col-6 d-flex align-items-center justify-content-end">
            
        </div>
    </div>
</section>

<div id="QR"></div>

<?php if (isset($_SESSION['ORDER_ID']) && $_SESSION['ORDER_ID'] != "") { ?>
<script>
function checkOpenStatus() {
    const xhttp = new XMLHttpRequest();
    
    xhttp.onload = function() {
        var result = this.responseText;
        if (result != 1) window.location.href = URL_ROOT + "/Order";
    }

    xhttp.open("GET", URL_ROOT + "/Order/checkOpenStatus");
    xhttp.send();

    setTimeout(checkOpenStatus, 2000);
}

checkOpenStatus();
</script>
<?php } ?>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>