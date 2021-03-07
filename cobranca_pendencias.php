<?php
    require_once 'init.php';

    $class             = new PendenciaService();
    $class->onPendencias();
    unset($class);

