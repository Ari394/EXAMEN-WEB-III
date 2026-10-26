<?php
// El borrado de carpetas lo hace el controlador Carpetas vía public/index.php.
// Un POST se reenvía con 307 para conservar el método y los datos del formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: ../public/index.php?action=borrar-carpeta', true, 307);
    exit;
}

header('Location: ../public/index.php?action=carpetas');
exit;
