<?php require_once APP_ROOT . '/helpers/Header.php';

$first_name = isset($_SESSION['CUSTOMER_FIRST_NAME']) ? $_SESSION['CUSTOMER_FIRST_NAME'] : "";
$last_name = isset($_SESSION['CUSTOMER_LAST_NAME']) ? $_SESSION['CUSTOMER_LAST_NAME'] : "";
$email = isset($_SESSION['CUSTOMER_EMAIL']) ? $_SESSION['CUSTOMER_EMAIL'] : "";
$table = isset($_SESSION['CUSTOMER_TABLE']) ? $_SESSION['CUSTOMER_TABLE'] : ""; ?>

<form id="checkout_form" action="<?php echo URL_ROOT; ?>/Checkout/startPayment" method="post">
    <div style="display: none;" class="tab">
        <h2 class="text-center mb-4">Bestellen</h2>

        <label class="mb-2" for="first_name">Voornaam</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" class="form-control mb-3" required>

        <label class="mb-2" for="last_name">Achternaam</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" class="form-control mb-3" required>

        <label class="mb-2" for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" class="form-control mb-3" required>
    </div>
    <div style="display: none;" class="tab">
        <h2 class="text-center mb-4">Selecteer tafel</h2>

        <label class="mb-2 w-100 text-center" for="table_number">Naar welke tafel mogen we je bestelling brengen?</label>
        <?php if (isset($data['table_number']) && $data['table_number'] != "") { ?>
        <input type="text" id="table" name="table_number" value="<?php echo $data['table_number']; ?>" class="form-control mb-3" required>
        <?php } else { ?>
        <input type="text" id="table" name="table_number" value="<?php echo $table; ?>" class="form-control mb-3" required>
        <?php } ?>
        <div id="invalid-feedback" class="invalid-feedback"></div>
    </div>
    <div style="display: none;" class="tab">
        <h2 class="text-center mb-4">Afrekenen</h2>

        <p class="text-center mb-4">Overzicht van je bestelling</p>

        <table class="table mb-3">
            <thead>
                <th>Product</th>
                <th>Aantal</th>
                <th>Bedrag</th>
            </thead>
            <tbody>
                <?php foreach ($data['orderlines'] as $orderline) { ?>
                <tr>
                    <td><?php echo $orderline->DESCRIPTION; ?></td>
                    <td><?php echo $orderline->AMOUNT; ?>x</td>
                    <td>€<?php echo number_format($orderline->PRICE * $orderline->AMOUNT, 2, ',', '.'); ?></td>
                </tr>
                <?php } ?>
                <tfoot>
                    <tr>
                        <th colspan="2">Totaal</th>
                        <th>€<?php echo number_format($data['total_price'], 2, ',', '.'); ?></th>
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            <button class="btn w-100 btn-secondary" type="button" id="prevBtn" onclick="nextPrev(-1)">Terug</button>
        </div>
        <div class="col">
            <button class="btn w-100 btn-primary" type="button" id="nextBtn" onclick="nextPrev(1)">Verder</button>
        </div>
    </div>
</form>

<script src="<?php echo URL_ROOT; ?>/js/Checkout.js"></script>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>