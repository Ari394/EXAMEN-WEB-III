
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Registro - myBox</title>
</head>
<body>

    <h1>Crear cuenta en myBox</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <form action="index.php?action=registrar"
          method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['csrf_token'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <label for="usuario">Usuario:</label>
        <input
            type="text"
            id="usuario"
            name="usuario"
            maxlength="50"
            autocomplete="username"
            required
        >

        <br><br>

        <label for="nombre">Nombre completo:</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            maxlength="100"
            autocomplete="name"
            required
        >

        <br><br>

        <label for="correo">Correo electrónico:</label>
        <input
            type="email"
            id="correo"
            name="correo"
            maxlength="150"
            autocomplete="email"
            required
        >

        <br><br>

        <label for="password">Contraseña:</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <br><br>

        <button type="submit">
            Registrarme
        </button>

    </form>

    <p>
        ¿Ya tienes cuenta?
        <a href="index.php?action=login">
            Inicia sesión
        </a>
    </p>

</body>
</html>
