<div class="alert alert-<?php echo $data['type']; ?> d-flex align-items-center" role="alert">
    <div class="row w-100">
        <div class="col-2 d-flex flex-column align-items-center justify-content-center">
            <img src="<?php echo URL_ROOT; ?>/images/icons/<?php echo $data['icon']; ?>.svg" alt="Bootstrap" width="32" height="32">
        </div>
        <?php if (isset($data['show_link']) && $data['show_link'] === true) { ?>
        <div class="col-10 d-flex flex-column justify-content-start">
        <?php } else { ?>
        <div class="col-10 d-flex align-items-center justify-content-start">
        <?php } ?>
            <p class="mb-0"><?php echo $data[$data['type'] . '_message']; ?></p>
            <?php if (isset($data['show_link']) && $data['show_link'] === true) { ?>
            <a class="btn btn-primary mt-2" href="<?php echo $data['link_href']; ?>"><?php echo $data['link_text']; ?></a>
            <?php } ?>
        </div>
    </div>
</div>