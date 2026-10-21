<?php
function compartido_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
$basePath = dirname($scriptPath);
if (strpos($scriptPath, '/app/views/carpetas/') !== false) {
    $basePath = dirname(dirname(dirname($basePath)));
}
$baseHref = rtrim(str_replace('\\', '/', $basePath), '/') . '/';
?>
<!doctype html>
<html lang="es">
<head>
    <base href="<?= htmlspecialchars($baseHref, ENT_QUOTES, 'UTF-8') ?>">
    <?php include __DIR__ . '/../../../sections/head.inc'; ?>
    <title>Compartido conmigo - MyBOX</title>
</head>
<body class="container-fluid">
<header class="row"><?php include __DIR__ . '/../../../sections/header.inc'; ?></header>
<main class="row"><div class="panel panel-primary">
    <div class="panel-heading"><strong>Compartido conmigo</strong></div>
    <div class="panel-body">
        <a class="btn btn-secondary" href="carpetas.php">Mis carpetas</a>
        <table class="table table-striped">
            <thead><tr><th>Nombre</th><th>Propietario</th><th>Fecha de creación</th></tr></thead>
            <tbody>
            <?php foreach ($carpetas as $carpeta): ?>
                <tr><td><?= compartido_h($carpeta['nombre']) ?></td><td><?= compartido_h($carpeta['propietario']) ?></td><td><?= compartido_h($carpeta['creada_en']) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$carpetas): ?><tr><td colspan="3">No han compartido carpetas contigo.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div></main>
<footer class="row"><?php include __DIR__ . '/../../../sections/foot.inc'; ?></footer>
</body>
</html>
