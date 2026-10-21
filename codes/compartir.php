<?php
session_start();
if (($_SESSION['autenticado'] ?? '') !== 'SI') {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/models/CarpetaCompartida.php';
require_once __DIR__ . '/../app/controllers/Compartir.php';

try {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id || !isset($_POST['usuario'])) {
        throw new InvalidArgumentException('Los datos para compartir la carpeta no son válidos.');
    }
    (new \MyBox\Controllers\Compartir())->share($id, $_SESSION['usuario'], $_POST['usuario']);
    header('Location: ../carpetas.php');
    exit;
} catch (Throwable $exception) {
    error_log('Unable to share folder: ' . $exception->getMessage());
    $_SESSION['error_compartir'] = $exception instanceof InvalidArgumentException || $exception instanceof RuntimeException
        ? $exception->getMessage()
        : 'No se pudo compartir la carpeta.';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    header('Location: ../app/views/carpetas/compartir.php?id=' . $id);
    exit;
}
