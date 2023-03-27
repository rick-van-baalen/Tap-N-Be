<?php if (count($data['orderlines']) > 0) { ?>
<table class="table table-striped order" id="order-table">
    <tbody id="order-tbody">
        <?php foreach ($data['orderlines'] as $orderline) { ?>
        <tr id="line_<?php echo $orderline->ID; ?>">
            <td class="icons">
                <h2 class="mb-2"><?php echo $orderline->DESCRIPTION; ?></h2>
                <?php if ($orderline->AMOUNT == 1) { ?>
                <span id="remove_<?php echo $orderline->PRODUCT_ID; ?>"><a href="javascript:void(0)" onclick="removeFromOrder(<?php echo $orderline->ID; ?>)"><img class="me-2" src="<?php echo URL_ROOT; ?>/images/icons/trash-fill.svg"></a></span>
                <?php } else { ?>
                    <span id="decrease_<?php echo $orderline->PRODUCT_ID; ?>"><a href="javascript:void(0)" onclick="decreaseAmount(<?php echo $orderline->PRODUCT_ID; ?>, <?php echo $orderline->ID; ?>, '<?php echo URL_ROOT; ?>')"><img class="me-2" src="<?php echo URL_ROOT; ?>/images/icons/dash-circle-fill.svg"></a></span>
                <?php } ?>
                <span id="count_<?php echo $orderline->PRODUCT_ID; ?>"><?php echo $orderline->AMOUNT; ?></span>
                <span id="increase_<?php echo $orderline->PRODUCT_ID; ?>"><a href="javascript:void(0)" onclick="increaseAmount(<?php echo $orderline->PRODUCT_ID; ?>, <?php echo $orderline->ID; ?>, '<?php echo URL_ROOT; ?>')"><img class="ms-2 me-4" src="<?php echo URL_ROOT; ?>/images/icons/plus-circle-fill.svg"></a></span>
            </td>
            <td class="text-end">
                €<span id="total_price_<?php echo $orderline->PRODUCT_ID; ?>"><?php echo number_format($orderline->PRICE * $orderline->AMOUNT, 2, ',', '.'); ?></span>
                <input type="hidden" id="price_<?php echo $orderline->PRODUCT_ID; ?>" value="<?php echo $orderline->PRICE; ?>">
            </td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="text-end"><b>Totaalprijs incl. BTW</b></td>
            <td class="text-end"><b>€<span id="totalWithVAT"><?php echo number_format($data['total_price'], 2, ',', '.'); ?></span></b></td>
        </tr>
    </tfoot>
</table>
<?php } else { ?>
<p class="text-center">Geen producten in je bestellijstje.</p>
<?php } ?>

<?php if (count($data['orderlines']) > 0) { ?>
<div class="row order-icons">
    <div class="col-3 d-flex align-items-center trash">
        <a class="btn w-100 btn-secondary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#clearOrder"><img src="<?php echo URL_ROOT; ?>/images/icons/trash.svg"></a>
    </div>
    <div class="col-9 d-flex align-items-center qr">
        <?php if (MODULE_PAYMENT) { ?>
        <a class="btn w-100 btn-primary" href="<?php echo URL_ROOT; ?>/Checkout">Bestellen</a>
        <?php } else { ?>
        <a class="btn w-100 btn-primary" href="javascript:void(0)" onclick="createQR()">Maak QR</a>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="clearOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form onsubmit="event.preventDefault(); clearOrder();">
                <div class="modal-header">
                    <h5 class="modal-title">Bestellijstje leegmaken</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 text-center">Weet je zeker dat je het bestellijstje leeg wilt maken?</p>
                </div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col">
                            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Nee</button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100">Ja</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>