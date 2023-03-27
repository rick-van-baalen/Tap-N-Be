<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<h2 class="text-center mb-3">Bestellijstje</h2>

<p class="text-center">Deze bestelling is reeds bevestigd. Je kunt niets meer doen met deze bestelling.</p>

<?php if ($data['is_customer'] === true) { ?>
<button type="button" onclick="newOrder();" class="btn btn-primary w-100">Nieuwe bestelling maken</button>
<?php } ?>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>