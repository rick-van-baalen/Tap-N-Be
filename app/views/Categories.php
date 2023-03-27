<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<h2 class="text-center mb-4">Waar heb je zin in?</h2>

<section class="mb-4">
    <div class="row">
        <?php foreach($data['categories'] as $category) { ?>
            <div class="col-6 col-xl-4 mb-3">
                <a href="<?php echo URL_ROOT . "/Menu/" . $category->SLUG; ?>" class="btn grid-btn"><?php echo $category->DESCRIPTION; ?></a>
            </div>
        <?php } ?>
    </div>
</section>

<section class="mb-5">
    <div class="row">
        <div class="col-6 d-flex align-items-center justify-content-start">
            <?php if (isset($data['breadcrumb'])) { ?>
            <a class="btn btn-back" href="<?php echo $data['breadcrumb']; ?>"><img src="<?php echo URL_ROOT; ?>/images/icons/arrow-left.svg"> Terug</a>
            <?php } ?>
        </div>
        <div class="col-6 d-flex align-items-center justify-content-end">
            <?php if (isset($data['category'])) { ?>
            <a class="btn w-100 btn-primary" href="<?php echo URL_ROOT; ?>/Menu/<?php echo $data['category']->SLUG; ?>/?view=all">Bekijk alles <img src="<?php echo URL_ROOT; ?>/images/icons/arrow-right.svg"></a>
            <?php } else { ?>
            <a class="btn w-100 btn-primary" href="<?php echo URL_ROOT; ?>/Menu/Assortiment">Bekijk alles <img src="<?php echo URL_ROOT; ?>/images/icons/arrow-right.svg"></a>
            <?php } ?>
        </div>
    </div>
</section>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>