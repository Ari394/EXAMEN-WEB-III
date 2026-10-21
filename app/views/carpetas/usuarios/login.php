
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - myBox</title>
</head>
<body>

    <h1>Iniciar sesión en myBox</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form action="/login" method="POST">

     
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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

    
        <label for="password">Contraseña:</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <br><br>

        
        <button type="submit">
            Ingresar
        </button>

    </form>

    <p>
        ¿No tienes cuenta?
        <a href="/registro">Regístrate aquí</a>
    </p>

</body>
</html>
