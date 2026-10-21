<?php
session_start();
if (($_SESSION['autenticado'] ?? '') !== 'SI') {
    header('Location: ../../../index.php');
    exit;
}
$error = $_SESSION['error_carpetas'] ?? '';
unset($_SESSION['error_carpetas']);
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
    <title>Nueva carpeta - MyBOX</title>
</head>
<body class="container-fluid">
<header class="row"><?php include __DIR__ . '/../../../sections/header.inc'; ?></header>
<main class="row"><div class="panel panel-primary">
    <div class="panel-heading"><strong>Nueva carpeta</strong></div>
    <div class="panel-body">
        <?php if ($error !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <form action="codes/creadir.php" method="post">
            <label for="nombre">Nombre de la carpeta</label>
            <input id="nombre" name="nombre" type="text" maxlength="100" required>
            <button class="btn btn-primary" type="submit">Crear carpeta</button>
            <a class="btn btn-secondary" href="carpetas.php">Cancelar</a>
        </form>
    </div>
</div></main>
<footer class="row"><?php include __DIR__ . '/../../../sections/foot.inc'; ?></footer>
</body>
</html>
