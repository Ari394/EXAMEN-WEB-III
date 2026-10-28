
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyBox - Carpetas</title>
    <link rel="stylesheet" href="../styles/mybox.css">
</head>
<body>
    <h1>MyBox</h1>
    <p>Hola, <?= h($_SESSION['nombre'] ?? '') ?></p>

    <form method="post" action="index.php?action=logout">
        <?= csrf() ?>
        <button type="submit">Salir</button>
    </form>

    <nav aria-label="Navegación principal">
        <a href="index.php?action=carpetas">Mis carpetas</a> |
        <a href="index.php?action=compartidas">Compartidas conmigo</a>
    </nav>

    <h2><?= $current === null ? 'Mi espacio' : 'Contenido de la carpeta' ?></h2>

    <?php if ($current !== null): ?>
        <nav aria-label="Ruta de carpetas">
            <a href="index.php?action=carpetas">Mi espacio</a>
            <?php foreach ($breadcrumbs as $breadcrumb): ?>
                &rsaquo;
                <a href="<?= h(folderUrl((int) $breadcrumb['id'])) ?>">
                    <?= h($breadcrumb['nombre']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($currentSharingStatus['compartida_conmigo']): ?>
            <p>Esta carpeta está compartida contigo. Tienes acceso de solo lectura.</p>
        <?php endif; ?>
    <?php endif; ?>

    <h3>Carpetas</h3>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Propietario</th>
                <th>Compartición</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carpetas as $folder): ?>
                <?php $status = $folderSharingStatuses[(int) $folder['id']]; ?>
                <tr>
                    <td>
                        <a href="<?= h(folderUrl((int) $folder['id'])) ?>">
                            <?= h($folder['nombre']) ?>
                        </a>
                    </td>
                    <td>Carpeta</td>
                    <td><?= h($folder['propietario']) ?></td>
                    <td>
                        <?php if ($status['compartida']): ?>
                            Compartida
                        <?php endif; ?>
                        <?php if ($status['compartida_conmigo']): ?>
                            <?= $status['compartida'] ? ' · ' : '' ?>Compartida conmigo
                        <?php endif; ?>
                        <?php if (!$status['compartida'] && !$status['compartida_conmigo']): ?>
                            Privada
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int) $folder['usuario_id'] === $userId): ?>
                            <form method="post" action="index.php?action=borrar-carpeta">
                                <?= csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $folder['id'] ?>">
                                <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
                                <button
                                    type="submit"
                                    onclick="return confirm('¿Borrar carpeta y todo su contenido?')"
                                >Borrar</button>
                            </form>

                            <form method="post" action="index.php?action=compartir">
                                <?= csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $folder['id'] ?>">
                                <label>
                                    Compartir con usuario
                                    <input
                                        name="usuario"
                                        maxlength="50"
                                        required
                                    >
                                </label>
                                <button type="submit">Compartir</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($carpetas === []): ?>
                <tr>
                    <td colspan="5">No hay carpetas en este nivel.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($canManageCurrent): ?>
        <h3>Nueva carpeta</h3>
        <form method="post" action="index.php?action=crear-carpeta">
            <?= csrf() ?>
            <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
            <label>
                Nombre
                <input name="nombre" maxlength="100" required>
            </label>
            <button type="submit">Crear</button>
        </form>

        <h3>Subir archivo (máximo 20 MiB)</h3>
        <form
            method="post"
            action="index.php?action=subir"
            enctype="multipart/form-data"
        >
            <?= csrf() ?>
            <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
            <label>
                Archivo
                <input type="file" name="archivo" required>
            </label>
            <label>
                Comentario
                <input name="comentario" maxlength="50">
            </label>
            <button type="submit">Subir</button>
        </form>
    <?php endif; ?>

    <h3>Archivos</h3>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Comentario</th>
                <th>Tipo de documento</th>
                <th>Peso (MB)</th>
                <th>Fecha de almacenamiento</th>
                <th>Compartición</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($archivos as $file): ?>
                <tr>
                    <td><?= h($file['nombre']) ?></td>
                    <td><?= h($file['comentario']) ?></td>
                    <td><?= h($file['tipo']) ?></td>
                    <td><?= number_format((int) $file['tamano'] / 1048576, 2) ?></td>
                    <td><?= h($file['fecha_subida']) ?></td>
                    <td>
                        <?php if ($currentSharingStatus['compartida']): ?>
                            Compartida
                        <?php endif; ?>
                        <?php if ($currentSharingStatus['compartida_conmigo']): ?>
                            <?= $currentSharingStatus['compartida'] ? ' · ' : '' ?>
                            Compartida conmigo
                        <?php endif; ?>
                        <?php if (
                            !$currentSharingStatus['compartida']
                            && !$currentSharingStatus['compartida_conmigo']
                        ): ?>
                            Privado
                        <?php endif; ?>
                    </td>
                    <td>
                        <a
                            href="index.php?action=visualizar&id=<?= (int) $file['id'] ?>"
                            target="_blank"
                            rel="noopener"
                        >
                            Ver archivo
                        </a>

                        <a href="index.php?action=descargar&id=<?= (int) $file['id'] ?>">
                            Descargar
                        </a>

                        <?php if ((int) $file['usuario_id'] === $userId): ?>
                            <form method="post" action="index.php?action=borrar-archivo">
                                <?= csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $file['id'] ?>">
                                <input type="hidden" name="folder" value="<?= h($current ?? '') ?>">
                                <button
                                    type="submit"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este archivo?')"
                                >
                                    Borrar
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($archivos === []): ?>
                <tr>
                    <td colspan="7">No hay archivos en este nivel.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

