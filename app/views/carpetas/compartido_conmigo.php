<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyBox - Compartido conmigo</title>
</head>
<body>
    <?php
        $scriptName = str_replace(
            '\\',
            '/',
            (string) ($_SERVER['SCRIPT_NAME'] ?? '')
        );
        $entryPoint = str_contains($scriptName, '/public/')
            ? 'index.php'
            : 'public/index.php';
    ?>
    <h1>Compartido conmigo</h1>
    <nav aria-label="Navegación principal">
        <a href="<?= htmlspecialchars($entryPoint, ENT_QUOTES, 'UTF-8') ?>?action=carpetas">
            Mis carpetas
        </a>
    </nav>

    <p>Las carpetas compartidas se pueden consultar, pero no modificar.</p>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Carpeta</th>
                <th>Propietario</th>
                <th>Compartida desde</th>
                <th>Acceso</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carpetas as $folder): ?>
                <tr>
                    <td>
                        <a href="<?= htmlspecialchars(
                            $entryPoint . '?action=carpetas&folder='
                                . (int) $folder['id'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">
                            <?= htmlspecialchars(
                                $folder['nombre'],
                                ENT_QUOTES | ENT_SUBSTITUTE,
                                'UTF-8'
                            ) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($folder['propietario'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($folder['fecha_creacion'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td>Solo lectura</td>
                </tr>
            <?php endforeach; ?>
            <?php if ($carpetas === []): ?>
                <tr><td colspan="4">Aún no hay carpetas compartidas contigo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
