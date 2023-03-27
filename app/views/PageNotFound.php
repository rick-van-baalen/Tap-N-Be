<?php require_once APP_ROOT . '/helpers/Header.php'; ?>

<section class="text-center">
    <h2 class="mb-3"><?php echo $data['title']; ?></h2>

    <p class="mb-4">Je bent naar een pagina geleid die niet (meer) bestaat. We helpen je graag de weg weer terug te vinden!</p>

    <a class="btn btn-primary" href="<?php echo URL_ROOT; ?>">Naar homepagina</a>
</section>

<?php require_once APP_ROOT . '/helpers/Footer.php'; ?>