<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<h2 class="text-center mb-4">Je betaling wordt verwerkt...</h2>

<div class="d-flex justify-content-center mb-4">
    <div class="spinner-border spinner-border-lg" role="status">
        <span class="visually-hidden">Je betaling wordt verwerkt...</span>
    </div>
</div>

<p class="text-center">Gelieve deze pagina niet te sluiten of te vernieuwen.</p>

<script>
function checkOpenPaymentStatus() {
    const xhttp = new XMLHttpRequest();
    
    xhttp.onload = function() {
        var result = this.responseText;
        if (result != 1) window.location.reload(1);
    }

    xhttp.open("GET", URL_ROOT + '/Checkout/checkOpenPaymentStatus/<?php echo $data['hashed_payment_id']; ?>');
    xhttp.send();

    setTimeout(checkOpenPaymentStatus, 2000);
}

checkOpenPaymentStatus();
</script>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>