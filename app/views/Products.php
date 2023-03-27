<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<h2 class="text-center mb-4"><?php echo $data['title']; ?></h2>

<section class="mb-4">
<?php if (count($data['products']) > 0) { ?>
    <table class="table products">
        <tbody>
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
                <td class="text-end">
                    <a href="javascript:void(0)" onclick="addToOrder(<?php echo $product->ID; ?>)"><img class="order-icon" src="<?php echo URL_ROOT; ?>/images/icons/plus-circle-fill.svg"></a>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
        <p class="text-center">Geen producten gevonden.</p>
    <?php } ?>
</section>

<section class="mb-5">
    <div class="row">
        <div class="col-6 d-flex align-items-center justify-content-start">
            <?php if (isset($data['breadcrumb'])) { ?>
            <a class="btn btn-back" href="<?php echo $data['breadcrumb']; ?>"><img src="<?php echo URL_ROOT; ?>/images/icons/arrow-left.svg"> Terug</a>
            <?php } ?>
        </div>
        <div class="col-6 d-flex align-items-center justify-content-end">
            
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>