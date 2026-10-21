<?php
declare(strict_types=1);

session_start();

spl_autoload_register(function (string $class): void {
    $prefix = 'MyBox\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $parts = explode('\\', $relative);

    if (count($parts) < 2) {
        return;
    }

    $parts[0] = strtolower($parts[0]);

    $file = dirname(__DIR__) . '/app/'
        . implode('/', $parts) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

use MyBox\Controllers\AuthWeb;
use MyBox\Controllers\Archivos;
use MyBox\Controllers\Carpetas;
use MyBox\Controllers\Compartir;

function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function folderId(mixed $value): ?int
{
    if ($value === null || $value === '') {
        return null;
    }

    $id = filter_var(
        $value,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($id === false) {
        throw new InvalidArgumentException(
            'Identificador de carpeta inválido.'
        );
    }

    return $id;
}

function requiredId(mixed $value): int
{
    $id = folderId($value);

    if ($id === null) {
        throw new InvalidArgumentException(
            'Falta el identificador.'
        );
    }

    return $id;
}

function folderUrl(?int $id = null): string
{
    return 'index.php?action=carpetas'
        . ($id !== null ? '&folder=' . $id : '');
}

function requireLogin(): int
{
    $id = (int) ($_SESSION['usuario_id'] ?? 0);

    if ($id < 1) {
        redirect('index.php?action=login');
    }

    return $id;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . h($_SESSION['csrf_token']) . '">';
}

function checkCsrf(): void
{
    $received = (string) ($_POST['csrf_token'] ?? '');
    $expected = (string) ($_SESSION['csrf_token'] ?? '');

    if (
        $received === '' ||
        $expected === '' ||
        !hash_equals($expected, $received)
    ) {
        throw new RuntimeException(
            'Solicitud inválida. Actualiza la página.',
            403
        );
    }
}

function allowedIp(): void
{
    $configured = trim((string) getenv('ALLOWED_IPS'));

    if ($configured === '') {
        return;
    }

    $allowed = array_filter(
        array_map('trim', explode(',', $configured)),
        fn(string $ip): bool => $ip !== ''
    );

    $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!in_array($currentIp, $allowed, true)) {
        http_response_code(403);
        exit('Acceso no permitido desde esta IP.');
    }
}

function requireOwnedFolder(
    int $userId,
    ?int $folderId
): void {
    if ($folderId === null) {
        return;
    }

    $db = \MyBox\Config\Database::connection();

    $query = $db->prepare(
        'SELECT id
         FROM carpetas
         WHERE id = ? AND usuario_id = ?'
    );

    $query->execute([$folderId, $userId]);

    if (!$query->fetchColumn()) {
        throw new RuntimeException(
            'No tienes permiso para acceder a esta carpeta.',
            403
        );
    }
}

allowedIp();

$action = (string) ($_GET['action'] ?? 'carpetas');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$postActions = [
    'autenticar',
    'registrar',
    'logout',
    'crear-carpeta',
    'borrar-carpeta',
    'compartir',
    'subir',
    'borrar-archivo'
];

try {
    if (in_array($action, $postActions, true)) {
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }

        checkCsrf();
    } elseif ($method !== 'GET') {
        http_response_code(405);
        exit('Método no permitido.');
    }

    $auth = new AuthWeb();

    switch ($action) {
        case 'login':
            if (!empty($_SESSION['usuario_id'])) {
                redirect(folderUrl());
            }

            $auth->login();
            break;

        case 'registro':
            if (!empty($_SESSION['usuario_id'])) {
                redirect(folderUrl());
            }

            $auth->registro();
            break;

        case 'autenticar':
            $auth->authenticate();
            break;

        case 'registrar':
            $auth->register();
            break;

        case 'logout':
            requireLogin();
            $auth->logout();
            break;

        case 'carpetas':
            $userId = requireLogin();
            $current = folderId($_GET['folder'] ?? null);
            $carpetasController = new Carpetas();
            $archivosController = new Archivos();

            $carpetas = $carpetasController->index(
                $userId,
                $current
            );

            $archivos = $archivosController->listForUser(
                $userId,
                $current
            );

            $breadcrumbs = $current === null
                ? []
                : $carpetasController->breadcrumbs(
                    $current,
                    $userId
                );
            $folderSharingStatuses = [];

            foreach ($carpetas as $folder) {
                $folderSharingStatuses[(int) $folder['id']]
                    = $carpetasController->sharingStatus(
                        (int) $folder['id'],
                        $userId
                    );
            }

            $currentSharingStatus = $current === null
                ? ['compartida' => false, 'compartida_conmigo' => false]
                : $carpetasController->sharingStatus($current, $userId);
            $canManageCurrent = $current === null
                || $carpetasController->owns($current, $userId);

            require dirname(__DIR__)
                . '/app/views/carpetas/listado.php';
            break;

        case 'compartidas':
            $userId = requireLogin();

            $carpetas = (new Carpetas())->sharedWith(
                $userId
            );

            require dirname(__DIR__)
                . '/app/views/carpetas/compartido_conmigo.php';
            break;

        case 'crear-carpeta':
            $userId = requireLogin();

            $parent = folderId(
                $_POST['folder'] ?? null
            );

            requireOwnedFolder($userId, $parent);

            (new Carpetas())->create(
                $userId,
                (string) ($_POST['nombre'] ?? ''),
                $parent
            );

            redirect(folderUrl($parent));

        case 'borrar-carpeta':
            $userId = requireLogin();

            $id = requiredId($_POST['id'] ?? null);

            requireOwnedFolder($userId, $id);

            (new Carpetas())->delete($id, $userId);

            $parent = folderId(
                $_POST['folder'] ?? null
            );

            requireOwnedFolder($userId, $parent);

            redirect(folderUrl($parent));

        case 'compartir':
            $userId = requireLogin();

            $id = requiredId($_POST['id'] ?? null);

            requireOwnedFolder($userId, $id);

            (new Compartir())->share(
                $id,
                $userId,
                (string) ($_POST['usuario'] ?? '')
            );

            redirect(folderUrl());

        case 'subir':
            $userId = requireLogin();

            $folder = folderId(
                $_POST['folder'] ?? null
            );

            requireOwnedFolder($userId, $folder);

            if (
                !isset($_FILES['archivo']) ||
                !is_array($_FILES['archivo'])
            ) {
                throw new RuntimeException(
                    'No se recibió ningún archivo.'
                );
            }

            (new Archivos())->upload(
                $userId,
                $folder,
                $_FILES['archivo'],
                (string) ($_POST['comentario'] ?? '')
            );

            redirect(folderUrl($folder));

        case 'descargar':
            $userId = requireLogin();

            $id = requiredId($_GET['id'] ?? null);

            $file = (new Archivos())->findById(
                $id,
                $userId
            );

            if (!$file) {
                http_response_code(404);
                exit('Archivo no encontrado.');
            }

            $filename = str_replace(
                ["\r", "\n", '"', '\\'],
                '',
                basename((string) $file['nombre'])
            );

            if ($filename === '') {
                $filename = 'archivo';
            }

            $content = $file['contenido'];

            if (is_resource($content)) {
                $content = stream_get_contents($content);
            }

            if (!is_string($content)) {
                throw new RuntimeException(
                    'No se pudo leer el archivo.'
                );
            }

            header('Content-Type: application/octet-stream');
            header('X-Content-Type-Options: nosniff');
            header(
                'Content-Disposition: attachment; filename="'
                . $filename . '"'
            );
            header('Content-Length: ' . strlen($content));

            echo $content;
            exit;

        case 'borrar-archivo':
            $userId = requireLogin();

            $id = requiredId($_POST['id'] ?? null);

            (new Archivos())->delete($id, $userId);

            $folder = folderId(
                $_POST['folder'] ?? null
            );

            requireOwnedFolder($userId, $folder);

            redirect(folderUrl($folder));

        default:
            http_response_code(404);
            echo 'Página no encontrada.';
    }
} catch (InvalidArgumentException $e) {
    http_response_code(422);

    echo '<h2>Datos inválidos</h2>';
    echo '<p>' . h($e->getMessage()) . '</p>';
    echo '<a href="index.php?action=carpetas">Volver</a>';
} catch (Throwable $e) {
    $status = (int) $e->getCode();

    if ($status < 400 || $status > 599) {
        $status = 500;
    }

    http_response_code($status);
    error_log((string) $e);

    echo '<h2>No se pudo completar la operación</h2>';

    if ($status === 403) {
        echo '<p>Acceso denegado o solicitud inválida.</p>';
    } else {
        echo '<p>Revisa los datos e inténtalo nuevamente.</p>';
    }

    echo '<a href="index.php?action=carpetas">Volver</a>';
}
