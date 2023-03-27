<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<div class="text-center">
    <h2 class="mb-4">Je bestelling</h2>

    <p>Hey <?php echo $data['customer']->FIRST_NAME; ?>,</p>
    <?php if (isset($data['table']->TABLE_NUMBER)) { ?>
    <p>Goed nieuws: je bestelling is succesvol geplaatst! Wij gaan ons best doen om je bestelling zo snel mogelijk bij je te brengen.</p>
    <p>Het tafelnummer wat je aan ons hebt doorgegeven is: <b><?php echo $data['table']->TABLE_NUMBER; ?></b>. Zit je niet (meer) aan deze tafel? Laat het ons zo spoedig mogelijk weten!</p>
    <?php } else { ?>
    <p>Goed nieuws: je bestelling is succesvol geplaatst! Wij gaan ons best doen om je bestelling zo snel mogelijk klaar te zetten.</p>
    <?php } ?>
    <p>Zie hieronder wat je besteld hebt:</p>
</div>

<table class="table mb-4">
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

<div class="text-center mb-5">
    <p>Wij hebben ook een bevestiging naar <?php echo $data['customer']->EMAIL; ?> gestuurd. Mocht er wat mis zijn met je bestelling, kun je deze e-mail als bon gebruiken.</p>
    <p>We zien je zo!</p>
    <p>Groet,</p>
    <p><?php echo SITE_NAME; ?></p>
</div>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>