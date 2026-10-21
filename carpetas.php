<?php
session_start();
if (($_SESSION['autenticado'] ?? '') !== 'SI') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/models/Carpeta.php';
require_once __DIR__ . '/app/models/Archivo.php';
require_once __DIR__ . '/app/controllers/Carpetas.php';
require_once __DIR__ . '/app/controllers/Archivos.php';

try {
    $carpetas = (new \MyBox\Controllers\Carpetas())->index($_SESSION['usuario']);
    $archivos = (new \MyBox\Controllers\Archivos())->listForUser($_SESSION['usuario']);
    require __DIR__ . '/app/views/carpetas/listado.php';
} catch (\Throwable $exception) {
    error_log('Unable to load folders: ' . $exception->getMessage());
    http_response_code(500);
    echo 'No se pudieron cargar las carpetas. Verifica la conexión de base de datos y la configuración de MyBox.';
}
