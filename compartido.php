<?php
session_start();
if (($_SESSION['autenticado'] ?? '') !== 'SI') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/models/CarpetaCompartida.php';
require_once __DIR__ . '/app/controllers/Compartir.php';

try {
    $carpetas = (new \MyBox\Controllers\Compartir())->sharedWith($_SESSION['usuario']);
    require __DIR__ . '/app/views/carpetas/compartido_conmigo.php';
} catch (\Throwable $exception) {
    error_log('Unable to load shared folders: ' . $exception->getMessage());
    http_response_code(500);
    echo 'No se pudieron cargar las carpetas compartidas.';
}
