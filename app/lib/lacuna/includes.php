<?php
require_once __DIR__ . '/config.php';
$config = getConfig();

// jQuery and other libs (used only to provide a better user experience, but NOT required to use the Web PKI component).
?>

<script>
    var _webPkiLicense = '<?= $config['webPki']['license']; ?>';
    var _restPkiEndpoint = '<?= $config['restPki']['endpoint'] ?>'
</script>
