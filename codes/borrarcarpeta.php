<?php
session_start();
if (($_SESSION['autenticado'] ?? '') !== 'SI') {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/models/Carpeta.php';
require_once __DIR__ . '/../app/controllers/Carpetas.php';

try {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new InvalidArgumentException('La carpeta indicada no es válida.');
    }
    if (!(new \MyBox\Controllers\Carpetas())->delete($id, $_SESSION['usuario'])) {
        throw new RuntimeException('La carpeta no existe o no te pertenece.');
    }
    header('Location: ../carpetas.php');
    exit;
} catch (Throwable $exception) {
    error_log('Unable to delete folder: ' . $exception->getMessage());
    http_response_code(400);
    $message = $exception instanceof InvalidArgumentException || $exception instanceof RuntimeException
        ? $exception->getMessage()
        : 'No se pudo borrar la carpeta.';
    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
}
