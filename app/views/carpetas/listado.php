<?php
use MyBox\Helpers\TipoDocumento;

$tituloPagina = 'Mis carpetas';
$seccion = $currentSharingStatus['compartida_conmigo'] ? 'compartidas' : 'carpetas';

$fecha = fn(?string $valor): string => $valor
    ? date('d/m/Y H:i', strtotime($valor))
    : '—';

$siNo = fn(bool $valor): string => $valor
    ? '<span class="badge bg-success">Sí</span>'
    : '<span class="badge bg-light text-muted border">No</span>';

// Raíz de la ruta: "Compartido conmigo" para el destinatario, "Mi espacio" para el dueño.
$raizUrl = $currentSharingStatus['compartida_conmigo']
    ? 'index.php?action=compartidas'
    : folderUrl();
$raizNombre = $currentSharingStatus['compartida_conmigo']
    ? 'Compartido conmigo'
    : 'Mi espacio';

// Nivel anterior: la carpeta padre si se puede leer; si no, la raíz.
$nivelAnteriorUrl = count($breadcrumbs) >= 2
    ? folderUrl((int) $breadcrumbs[count($breadcrumbs) - 2]['id'])
    : $raizUrl;

require __DIR__ . '/partials/encabezado.php';
?>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h1 class="h4 mb-0">
                <?= $current === null ? 'Mi espacio' : h(end($breadcrumbs)['nombre'] ?? 'Carpeta') ?>
            </h1>
            <?php if ($current !== null): ?>
                <a class="btn btn-outline-secondary btn-sm" href="<?= h($nivelAnteriorUrl) ?>">
                    &larr; Nivel anterior
                </a>
            <?php endif; ?>
        </div>

        <nav aria-label="Ruta de carpetas">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <a href="<?= h($raizUrl) ?>"><?= h($raizNombre) ?></a>
                </li>
                <?php foreach ($breadcrumbs as $indice => $breadcrumb): ?>
                    <?php if ($indice === count($breadcrumbs) - 1): ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= h($breadcrumb['nombre']) ?>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="<?= h(folderUrl((int) $breadcrumb['id'])) ?>">
                                <?= h($breadcrumb['nombre']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>

        <?php if ($currentSharingStatus['compartida_conmigo']): ?>
            <div class="alert alert-info py-2">
                Esta carpeta está compartida contigo. Tienes acceso de solo lectura.
            </div>
        <?php endif; ?>

        <?php if ($canManageCurrent): ?>
            <div class="d-flex flex-wrap align-items-end gap-3 mb-3">
                <a class="btn btn-primary"
                   href="index.php?action=nueva-carpeta<?= $current !== null ? '&amp;folder=' . (int) $current : '' ?>">
                    + Nueva carpeta
                </a>

                <form method="post" action="index.php?action=subir"
                      enctype="multipart/form-data"
                      class="d-flex flex-wrap align-items-end gap-2">
                    <?= csrf() ?>
                    <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
                    <div>
                        <label class="form-label small mb-0" for="archivo">Archivo (máximo 20 MiB)</label>
                        <input class="form-control form-control-sm" type="file"
                               id="archivo" name="archivo" required>
                    </div>
                    <div>
                        <label class="form-label small mb-0" for="comentario">Comentario</label>
                        <input class="form-control form-control-sm" id="comentario"
                               name="comentario" maxlength="50">
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">Subir</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Comentario</th>
                        <th>Tipo de documento</th>
                        <th class="text-end">Peso (MB)</th>
                        <th>Fecha de almacenamiento</th>
                        <th>Compartido</th>
                        <th>Compartido conmigo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carpetas as $folder): ?>
                        <?php $status = $folderSharingStatuses[(int) $folder['id']]; ?>
                        <tr>
                            <td><span class="badge bg-warning text-dark">Directorio</span></td>
                            <td>
                                <a class="fw-semibold" href="<?= h(folderUrl((int) $folder['id'])) ?>">
                                    <?= h($folder['nombre']) ?>
                                </a>
                            </td>
                            <td class="text-muted">—</td>
                            <td>Carpeta</td>
                            <td class="text-end text-muted">—</td>
                            <td><?= h($fecha($folder['fecha_creacion'])) ?></td>
                            <td><?= $siNo($status['compartida']) ?></td>
                            <td><?= $siNo($status['compartida_conmigo']) ?></td>
                            <td class="text-nowrap">
                                <a class="btn btn-outline-primary btn-sm"
                                   href="<?= h(folderUrl((int) $folder['id'])) ?>">Abrir</a>
                                <?php if ((int) $folder['usuario_id'] === $userId): ?>
                                    <a class="btn btn-outline-secondary btn-sm"
                                       href="index.php?action=compartir-carpeta&amp;id=<?= (int) $folder['id'] ?><?= $current !== null ? '&amp;folder=' . (int) $current : '' ?>">
                                        Compartir
                                    </a>
                                    <form method="post" action="index.php?action=borrar-carpeta" class="d-inline">
                                        <?= csrf() ?>
                                        <input type="hidden" name="id" value="<?= (int) $folder['id'] ?>">
                                        <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('¿Borrar carpeta y todo su contenido?')">
                                            Borrar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php foreach ($archivos as $file): ?>
                        <tr>
                            <td><span class="badge bg-secondary">Archivo</span></td>
                            <td><?= h($file['nombre']) ?></td>
                            <td><?= h($file['comentario'] ?? '') ?></td>
                            <td title="<?= h($file['tipo']) ?>">
                                <?= h(TipoDocumento::describir((string) $file['tipo'], (string) $file['nombre'])) ?>
                            </td>
                            <td class="text-end"><?= number_format((int) $file['tamano'] / 1048576, 2) ?></td>
                            <td><?= h($fecha($file['fecha_subida'])) ?></td>
                            <td><?= $siNo($currentSharingStatus['compartida']) ?></td>
                            <td><?= $siNo($currentSharingStatus['compartida_conmigo']) ?></td>
                            <td class="text-nowrap">
                                <?php if (TipoDocumento::esVisualizable((string) $file['tipo'])): ?>
                                    <a class="btn btn-outline-primary btn-sm"
                                       href="index.php?action=visualizar&amp;id=<?= (int) $file['id'] ?>"
                                       target="_blank"
                                       rel="noopener">
                                        Ver archivo
                                    </a>
                                <?php endif; ?>

                                <a class="btn btn-outline-secondary btn-sm"
                                   href="index.php?action=descargar&amp;id=<?= (int) $file['id'] ?>">
                                    Descargar
                                </a>

                                <?php if ((int) $file['usuario_id'] === $userId): ?>
                                    <form method="post" action="index.php?action=borrar-archivo" class="d-inline">
                                        <?= csrf() ?>
                                        <input type="hidden" name="id" value="<?= (int) $file['id'] ?>">
                                        <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar este archivo?')">
                                            Borrar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($carpetas === [] && $archivos === []): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Esta carpeta está vacía.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/pie.php'; ?>
