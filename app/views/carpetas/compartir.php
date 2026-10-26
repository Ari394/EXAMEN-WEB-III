<?php
$tituloPagina = 'Compartir carpeta';
$seccion = 'carpetas';
require __DIR__ . '/partials/encabezado.php';
?>
<div class="card shadow-sm mybox-formulario">
    <div class="card-header bg-primary text-white">
        <strong>Compartir carpeta: <?= h($carpeta['nombre'] ?? '') ?></strong>
    </div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>
        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-success"><?= h($mensaje) ?></div>
        <?php endif; ?>

        <p class="text-muted small">
            El usuario elegido podrá ver esta carpeta, sus subcarpetas y sus archivos
            (solo lectura).
        </p>

        <?php if ($usuarios === []): ?>
            <div class="alert alert-secondary">No hay más usuarios registrados con quien compartir.</div>
        <?php else: ?>
            <form method="post" action="index.php?action=compartir" class="d-flex flex-wrap gap-2 mb-4">
                <?= csrf() ?>
                <input type="hidden" name="id" value="<?= (int) $carpeta['id'] ?>">
                <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
                <label class="visually-hidden" for="usuario">Usuario</label>
                <select class="form-select w-auto" id="usuario" name="usuario" required>
                    <option value="">Elige un usuario…</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= h($usuario['usuario']) ?>">
                            <?= h($usuario['usuario']) ?> — <?= h($usuario['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Compartir</button>
            </form>
        <?php endif; ?>

        <h2 class="h6">Compartida con</h2>
        <?php if ($destinatarios === []): ?>
            <p class="text-muted">Todavía no la compartiste con nadie.</p>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($destinatarios as $destinatario): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <strong><?= h($destinatario['usuario']) ?></strong>
                            — <?= h($destinatario['nombre']) ?>
                            <small class="text-muted">
                                (desde <?= h(date('d/m/Y H:i', strtotime((string) $destinatario['fecha_compartido']))) ?>)
                            </small>
                        </span>
                        <form method="post" action="index.php?action=dejar-compartir">
                            <?= csrf() ?>
                            <input type="hidden" name="id" value="<?= (int) $carpeta['id'] ?>">
                            <input type="hidden" name="usuario_id" value="<?= (int) $destinatario['id'] ?>">
                            <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('¿Dejar de compartir con este usuario?')">
                                Dejar de compartir
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="btn btn-secondary" href="<?= h(folderUrl($current)) ?>">Volver</a>
    </div>
</div>
<?php require __DIR__ . '/partials/pie.php'; ?>
