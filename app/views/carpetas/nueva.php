<?php
$tituloPagina = 'Nueva carpeta';
$seccion = 'carpetas';
require __DIR__ . '/partials/encabezado.php';
?>
<div class="card shadow-sm mybox-formulario">
    <div class="card-header bg-primary text-white"><strong>Nueva carpeta</strong></div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?action=crear-carpeta">
            <?= csrf() ?>
            <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
            <div class="mb-3">
                <label class="form-label" for="nombre">Nombre de la carpeta</label>
                <input class="form-control" id="nombre" name="nombre"
                       maxlength="100" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Crear carpeta</button>
            <a class="btn btn-secondary" href="<?= h(folderUrl($current)) ?>">Cancelar</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/partials/pie.php'; ?>
