        </div>

        <?php if ((isset($_SESSION['show_instruction']) && $_SESSION['show_instruction'] === true) && (MODULE_QR || MODULE_PAYMENT)) { $_SESSION['show_instruction'] = false; ?>
        
        <div class="modal fade" id="showInstruction" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Welkom bij <?php echo SITE_NAME; ?>!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Het bestellen werkt als volgt:</p>
                        <ol>
                            <li>Kies je gewenste product.</li>
                            <li>Voeg het toe aan je bestellijst.</li>
                            <?php if (MODULE_PAYMENT) { ?>
                            <li>Betaal je bestelling gemakkelijk met iDEAL.</li>
                            <li>Na je betaling komen wij je bestelling zo snel mogelijk brengen.</li>
                            <?php } else { ?>
                            <li>Maak een QR code en laat deze scannen door ons personeel.</li>
                            <?php } ?>
                            <li>Geniet van je bestelling!</li>
                        </ol>
                        <p class="m-0">Kom je er niet uit? Bestellen bij ons personeel is uiteraard ook mogelijk!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ik begrijp het</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

    </body>

    <script src="<?php echo URL_ROOT; ?>/js/popper.min.js"></script>
    <script src="<?php echo URL_ROOT; ?>/js/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo URL_ROOT; ?>/js/jquery-3.6.0.min.js"></script>
    <script src="<?php echo URL_ROOT; ?>/js/Functions.js"></script>
    
</html>