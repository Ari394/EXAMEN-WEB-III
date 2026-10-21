<?php
    $error = '';
    if (isset($_POST['txtUsua'], $_POST['txtContra'], $_POST['txtNomb'], $_POST['txtEmail'])) {
        require_once __DIR__ . '/app/config/Database.php';
        require_once __DIR__ . '/app/models/Usuario.php';
        require_once __DIR__ . '/app/controllers/Usuarios.php';
        try {
            $username = trim($_POST['txtUsua']);
            $name = trim($_POST['txtNomb']);
            $email = trim($_POST['txtEmail']);
            if ($username === '' || strlen($username) > 15 || $name === '' || strlen($name) > 30 ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
                throw new InvalidArgumentException('Revisa los datos del formulario e inténtalo nuevamente.');
            }
            (new \MyBox\Controllers\Usuarios())->register($username, $_POST['txtContra'], $name, $email);
            session_start();
            $_SESSION['autenticado'] = 'SI';
            $_SESSION['nombre'] = $name;
            $_SESSION['usuario'] = $username;
            $_SESSION['ruta'] = getenv('HOME_PATH') . '/' . $username;
            header('Location: codes/creadir.php');
            exit;
        } catch (\Throwable $exception) {
            error_log('Unable to register user: ' . $exception->getMessage());
            $error = $exception instanceof InvalidArgumentException
                ? $exception->getMessage()
                : 'No se pudo completar el registro. Comprueba si el usuario ya existe.';
        }
    }
?>
<!doctype html>
<html lang="en">
<head>
    <?php
        include_once('sections/head.inc');
    ?>
    <title>Registrarse al Sitio</title>
</head>
<body class="container-fluid">
    <header class="row">
        <div class="row">
            <?php include_once('sections/header.inc'); ?>
        </div>
    </header>
    <main class="row">
        <div class="panel panel-primary datos3">
            <div class="panel-heading">
                <strong>Datos del nuevo usuario</strong>
            </div>
            <div class="panel-body">
                <?php if ($error !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset>
                        <label>Usuario:</label><input type="text" name="txtUsua" size="22" maxlength="15" required /><br>
                        <label>Contraseña:</label><input type="password" name="txtContra" size="22" maxlength="15" required /><br>
                        <label>Nombre Completo:</label><input type="text" name="txtNomb" size="40" maxlength="30" required /><br>
                        <label>Correo Electrónico:</label><input type="text" name="txtEmail" size="50" maxlength="50" required /><br>
                    </fieldset>
                    <input type="submit" value="Aceptar" />
                </form>
            </div>
        </div>
    </main>

    <footer class="row">
        <?php include_once('sections/foot.inc'); ?>
    </footer>
</body>
</html>
