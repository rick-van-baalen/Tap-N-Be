<?php if (count($data['products']) != 0) { ?>
<table class="table products">
    <tbody id="results-tbody">
        <?php foreach ($data['products'] as $product) { ?>
        <tr>
            <td class="image">
                <img src="<?php echo ADMIN_ROOT . "/" . $product->IMAGE; ?>">
            </td>
            <td>
            <h4 class="title"><?php echo $product->DESCRIPTION; ?></h4>
            <?php if (isset($product->USED_FEATURES)) { ?>
            <p class="terms">
                <?php foreach ($product->USED_FEATURES as $used_feature) {
                    if ($used_feature->ID == $product->USED_FEATURES[count($product->USED_FEATURES) - 1]->ID) {
                        echo $used_feature->DESCRIPTION . ": " . $used_feature->VALUE . $used_feature->AFTER;
                    } else {
                        echo $used_feature->DESCRIPTION . ": " . $used_feature->VALUE . $used_feature->AFTER . " | ";
                    }
                } ?>
            </p>
            <?php } ?>
            </td>
            <td>
                <p class="price m-0"><b>€<?php echo number_format($product->PRICE, 2, ',', '.'); ?></b></p>
            </td>
            <?php if (MODULE_QR || MODULE_PAYMENT) { ?>
            <td>
                <a href="javascript:void(0)" onclick="addToOrder(<?php echo $product->ID; ?>)"><img class="order-icon" src="<?php echo URL_ROOT; ?>/images/icons/plus-circle-fill.svg"></a>
            </td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } else { ?>
<p class="m-0 mt-4">Helaas! Niets gevonden...</p>
<?php } ?>