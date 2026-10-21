
<?php
namespace MyBox\Controllers;

use PDOException;

final class AuthWeb
{
    public function login(): void
    {
        $error = '';
        require dirname(__DIR__) . '/views/usuarios/login.php';
    }

    public function authenticate(): void
    {
        $this->verifyCsrf();

        $username = (string) ($_POST['usuario'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $controller = new Usuarios();
        $user = $controller->authenticate($username, $password);

        if ($user === null) {
            http_response_code(401);
            $error = 'Usuario o contraseña incorrectos.';
            require dirname(__DIR__) . '/views/usuarios/login.php';
            return;
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = (int) $user['id'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['nombre'] = $user['nombre'];

        header('Location: /carpetas');
        exit;
    }

    public function registro(): void
    {
        $error = '';
        require dirname(__DIR__) . '/views/usuarios/registro.php';
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $username = (string) ($_POST['usuario'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $name = (string) ($_POST['nombre'] ?? '');
        $email = (string) ($_POST['correo'] ?? '');

        try {
            (new Usuarios())->register(
                $username,
                $password,
                $name,
                $email
            );

            header('Location: /login');
            exit;
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $error = $exception->getMessage();
        } catch (PDOException $exception) {
            http_response_code(400);
            $error = 'No se pudo registrar. Verifica si el usuario o correo ya existe.';
        }

        require dirname(__DIR__) . '/views/usuarios/registro.php';
    }

    public function logout(): void
    {
        $this->verifyCsrf();

        $_SESSION = [];
        session_regenerate_id(true);

        header('Location: /login');
        exit;
    }

    private function verifyCsrf(): void
    {
        $submitted = (string) ($_POST['csrf_token'] ?? '');
        $expected = (string) ($_SESSION['csrf_token'] ?? '');

        if (
            $expected === '' ||
            !hash_equals($expected, $submitted)
        ) {
            http_response_code(403);
            exit('Solicitud no válida.');
        }
    }
}
