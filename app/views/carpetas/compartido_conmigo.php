<?php
$tituloPagina = 'Compartido conmigo';
$seccion = 'compartidas';
require __DIR__ . '/partials/encabezado.php';
?>
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4">Compartido conmigo</h1>
        <p class="text-muted">
            Carpetas que otros usuarios compartieron contigo. Puedes abrirlas, navegar
            sus subcarpetas, ver y descargar archivos, pero no modificarlas.
        </p>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Carpeta</th>
                        <th>Propietario</th>
                        <th>Compartida el</th>
                        <th>Acceso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carpetas as $folder): ?>
                        <tr>
                            <td>
                                <span class="badge bg-warning text-dark me-1">Directorio</span>
                                <a class="fw-semibold" href="<?= h(folderUrl((int) $folder['id'])) ?>">
                                    <?= h($folder['nombre']) ?>
                                </a>
                            </td>
                            <td><?= h($folder['propietario']) ?></td>
                            <td><?= h(date('d/m/Y H:i', strtotime((string) $folder['fecha_compartido']))) ?></td>
                            <td><span class="badge bg-info text-dark">Solo lectura</span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($carpetas === []): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Aún no hay carpetas compartidas contigo.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/pie.php'; ?>
