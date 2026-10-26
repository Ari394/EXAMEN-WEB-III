<?php
// La vista de carpetas la atiende el controlador frontal MVC (public/index.php),
// que prepara permisos, breadcrumbs y estado de compartición.
$url = 'public/index.php?action=carpetas';

$folder = filter_input(INPUT_GET, 'folder', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if ($folder) {
    $url .= '&folder=' . $folder;
}

header('Location: ' . $url);
exit;
