<?php
    $Accion_Formulario = $_SERVER['PHP_SELF'];
    $error = '';
    if (isset($_POST['txtUsua'], $_POST['txtContra'])) {
        require_once __DIR__ . '/app/config/Database.php';
        require_once __DIR__ . '/app/models/Usuario.php';
        require_once __DIR__ . '/app/controllers/Usuarios.php';
        try {
            $user = (new \MyBox\Controllers\Usuarios())->authenticate($_POST['txtUsua'], $_POST['txtContra']);
            if ($user !== null) {
                session_start();
                $_SESSION['autenticado'] = 'SI';
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['usuario'] = $user['usuario'];
                header('Location: carpetas.php');
                exit;
            }
            $error = 'El usuario o la contraseña no son correctos.';
        } catch (\Throwable $exception) {
            error_log('Unable to authenticate user: ' . $exception->getMessage());
            http_response_code(500);
            $error = 'No se pudo iniciar sesión. Inténtalo más tarde.';
        }
    }
?>
<!doctype html>
<html lang="en">
<head>
    <?php
        include_once('sections/head.inc');
    ?>
    <title>Ingreso al Sitio</title>
</head>
<body class="container-fluid">
    <header class="row">
        <div class="row">
            <?php include_once('sections/header.inc'); ?>
        </div>
    </header>
    <main class="row">
        <div class="panel panel-primary logueo">
            <div class="panel-heading">
                <strong>Autenticación de Usuarios</strong>
            </div>
            <div class="panel-body">
                <?php if ($error !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                <form action="<?php echo $Accion_Formulario; ?>" method="post">
                    <fieldset>
                        <label>Usuario:</label><input type="text" name="txtUsua" size="22" maxlength="15" required /><br>
                        <label>Contraseña:</label><input type="password" name="txtContra" size="22" maxlength="15" required />
                    </fieldset>
                    <input type="submit" value="Aceptar" />
                </form>
            </div>
            <br>
            <a href="registrar.php">Registrarse Aquí</a>
        </div>

    </main>

    <footer class="row">
        <?php include_once('sections/foot.inc'); ?>
    </footer>
</body>
</html>
