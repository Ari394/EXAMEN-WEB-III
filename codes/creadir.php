<?php
declare(strict_types=1);

session_start();

function redirectToFolders(?int $folderId = null): never
{
    $url = '../index.php?action=carpetas';
    if ($folderId !== null) {
        $url .= '&folder=' . $folderId;
    }

    header('Location: ' . $url);
    exit;
}

if ((int) ($_SESSION['usuario_id'] ?? 0) < 1) {
    header('Location: ../index.php?action=login');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

$expectedToken = (string) ($_SESSION['csrf_token'] ?? '');
$receivedToken = (string) ($_POST['csrf_token'] ?? '');
if (
    $expectedToken === ''
    || $receivedToken === ''
    || !hash_equals($expectedToken, $receivedToken)
) {
    http_response_code(403);
    exit('Solicitud inválida. Actualiza la página e inténtalo de nuevo.');
}

$parentValue = $_POST['folder'] ?? null;
$parentId = null;
if ($parentValue !== null && $parentValue !== '') {
    $parentId = filter_var(
        $parentValue,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );
    if ($parentId === false) {
        http_response_code(422);
        exit('Identificador de carpeta inválido.');
    }
}

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/models/Carpeta.php';
require_once __DIR__ . '/../app/controllers/Carpetas.php';

try {
    (new \MyBox\Controllers\Carpetas())->create(
        (int) $_SESSION['usuario_id'],
        (string) ($_POST['nombre'] ?? ''),
        $parentId
    );

    redirectToFolders($parentId);
} catch (InvalidArgumentException $exception) {
    http_response_code(422);
    echo htmlspecialchars(
        $exception->getMessage(),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
} catch (Throwable $exception) {
    error_log('Unable to create folder: ' . $exception);
    http_response_code($exception->getCode() === 403 ? 403 : 500);
    echo 'No se pudo crear la carpeta. Revisa tus permisos e inténtalo nuevamente.';
}
