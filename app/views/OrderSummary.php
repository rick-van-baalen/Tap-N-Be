<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<h2 class="text-center mb-3">Bestellijstje</h2>

<?php if (isset($data['table_number'])) { ?>
<p class="text-center">Tafelnummer: <b><?php echo $data['table_number']; ?></b><br>
<?php if (isset($data['last_modified'])) { ?>
Laatst gewijzigd: <b><?php echo $data['last_modified']; ?></b>
<?php } ?>
</p>
<?php } ?>

<section id="order" class="container">
    <?php if (isset($data['orderlines']) && count($data['orderlines']) > 0) { ?>
    <table class="table table-striped order" id="order-table">
        <tbody>
            <?php foreach ($data['orderlines'] as $orderline) { ?>
            <tr>
                <td class="image"><img src="<?php echo ADMIN_ROOT . "/" . $orderline->IMAGE; ?>"></td>
                <td><b><?php echo $orderline->AMOUNT; ?>x</b> <?php echo $orderline->DESCRIPTION; ?></td>
                <td class="text-end">€<?php echo number_format($orderline->PRICE * $orderline->AMOUNT, 2, ',', '.'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-end"><b>Totaalprijs incl. BTW</b></td>
                <td class="text-end"><b>€<span id="totalWithVAT"><?php echo number_format($data['total_price'], 2, ',', '.'); ?></span></b></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="row">
        <div class="col">
            <a data-bs-toggle="modal" data-bs-target="#cancelOrder" class="btn btn-secondary btn-lg w-100" href="javascript:void(0)">Annuleren</a>
        </div>
        <div class="col">
            <a data-bs-toggle="modal" data-bs-target="#completeOrder" class="btn btn-primary btn-lg w-100" href="javascript:void(0)">Afhandelen</a>
        </div>
    </div>
    <?php } else { ?>
    <p class="text-center">Geen producten in dit bestellijstje.</p>
    <?php } ?>
</section>

<!-- Cancel order -->
<div class="modal fade" id="cancelOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order annuleren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Weet je zeker dat je deze order wilt annuleren?</p>
            </div>
            <div class="modal-footer">
                <form class="w-100" onsubmit="event.preventDefault(); cancelOrder(<?php echo $data['order_id']; ?>);">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-secondary btn-lg w-100" data-bs-dismiss="modal">Nee</button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-lg w-100">Ja</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Complete order -->
<div class="modal fade" id="completeOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order afhandelen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Weet je zeker dat je deze order wilt afhandelen?</p>
            </div>
            <div class="modal-footer">
                <form class="w-100" onsubmit="event.preventDefault(); completeOrder(<?php echo $data['order_id']; ?>);">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-secondary btn-lg w-100" data-bs-dismiss="modal">Nee</button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-lg w-100">Ja</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>