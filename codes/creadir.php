<?php
    session_start();
if (($_SESSION['autenticado'] ?? '') !== 'SI') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../app/config/Database.php';
    require_once __DIR__ . '/../app/models/Carpeta.php';
    require_once __DIR__ . '/../app/controllers/Carpetas.php';

    try {
        if (!isset($_POST['nombre'])) {
            throw new InvalidArgumentException('Indica el nombre de la carpeta.');
        }
        (new \MyBox\Controllers\Carpetas())->create($_SESSION['usuario'], $_POST['nombre']);
        header('Location: ../carpetas.php');
        exit;
    } catch (Throwable $exception) {
        error_log('Unable to create folder: ' . $exception->getMessage());
        $_SESSION['error_carpetas'] = $exception instanceof InvalidArgumentException
            ? $exception->getMessage()
            : 'No se pudo crear la carpeta. Comprueba que no exista otra con el mismo nombre.';
        header('Location: ../app/views/carpetas/nueva.php');
        exit;
    }
}

$home = getenv('HOME_PATH');
if ($home === false || $home === '') {
    http_response_code(500);
    exit('La variable HOME_PATH no está configurada.');
}
$ruta = rtrim($home, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $_SESSION['usuario'];
if (!is_dir($ruta) && !mkdir($ruta, 0700, true) && !is_dir($ruta)) {
    error_log('Unable to create user storage directory: ' . $ruta);
    http_response_code(500);
    exit('No se pudo crear el directorio para almacenar tus archivos.');
}
header('Location: ../carpetas.php');
exit;
?>
