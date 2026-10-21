<?php
function carpetas_h(string $value): string
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
    <title>Mis carpetas - MyBOX</title>
</head>
<body class="container-fluid">
<header class="row"><?php include __DIR__ . '/../../../sections/header.inc'; ?></header>
<main class="row">
    <div class="panel panel-primary">
        <div class="panel-heading"><strong>Mis carpetas</strong></div>
        <div class="panel-body">
            <p>
                <a class="btn btn-primary" href="app/views/carpetas/nueva.php">Nueva carpeta</a>
                <a class="btn btn-secondary" href="compartido.php">Compartido conmigo</a>
                <a class="btn btn-secondary" href="agrearchi.php">Agregar archivo</a>
            </p>
            <?php if (!empty($error)): ?><div class="alert alert-danger"><?= carpetas_h($error) ?></div><?php endif; ?>
            <table class="table table-striped">
                <thead><tr><th>Nombre</th><th>Propietario</th><th>Fecha de creación</th><th>Compartir</th><th>Acción</th></tr></thead>
                <tbody>
                <?php foreach ($carpetas as $carpeta): ?>
                    <tr>
                        <td><?= carpetas_h($carpeta['nombre']) ?></td>
                        <td><?= carpetas_h($carpeta['usuario']) ?></td>
                        <td><?= carpetas_h($carpeta['creada_en']) ?></td>
                        <td><a href="app/views/carpetas/compartir.php?id=<?= (int) $carpeta['id'] ?>">Compartir</a></td>
                        <td>
                            <form action="codes/borrarcarpeta.php" method="post" onsubmit="return confirm('¿Eliminar esta carpeta?');">
                                <input type="hidden" name="id" value="<?= (int) $carpeta['id'] ?>">
                                <button class="btn btn-danger btn-sm" type="submit">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$carpetas): ?><tr><td colspan="5">Aún no tienes carpetas.</td></tr><?php endif; ?>
                </tbody>
            </table>
            <h2>Archivos de tu espacio</h2>
            <?php if (!empty($archivos)): ?>
                <table class="table table-striped">
                    <thead><tr><th>Nombre</th><th>Tamaño</th><th>Último acceso</th><th>Archivo</th><th>Directorio</th><th>Lectura</th><th>Escritura</th><th>Ejecutable</th><th>Acción</th></tr></thead>
                    <tbody>
                    <?php foreach ($archivos as $archivo): ?>
                        <tr>
                            <td><a href="abrarchi.php?arch=<?= rawurlencode($archivo['nombre']) ?>"><?= carpetas_h($archivo['nombre']) ?></a></td>
                            <td><?= (int) $archivo['tamano'] ?> bytes</td>
                            <td><?= date('d/m/Y H:i:s', (int) $archivo['ultimo_acceso']) ?></td>
                            <td><?= $archivo['archivo'] ? 'Sí' : 'No' ?></td>
                            <td><?= $archivo['directorio'] ? 'Sí' : 'No' ?></td>
                            <td><?= $archivo['lectura'] ? 'Sí' : 'No' ?></td>
                            <td><?= $archivo['escritura'] ? 'Sí' : 'No' ?></td>
                            <td><?= $archivo['ejecutable'] ? 'Sí' : 'No' ?></td>
                            <td><a href="codes/borrarchi.php?archi=<?= rawurlencode($archivo['nombre']) ?>">Borrar</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>La carpeta del usuario se encuentra vacía.</p>
            <?php endif; ?>
        </div>
    </div>
</main>
<footer class="row"><?php include __DIR__ . '/../../../sections/foot.inc'; ?></footer>
</body>
</html>
