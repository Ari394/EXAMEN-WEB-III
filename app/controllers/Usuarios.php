
<?php
namespace MyBox\Controllers;

use MyBox\Config\Database;
use MyBox\Models\Usuario;
use InvalidArgumentException;

final class Usuarios
{
    
    public function authenticate(
        string $username,
        string $password
    ): ?array {
        if (trim($username) === '' || $password === '') {
            return null;
        }

        $model = new Usuario(Database::connection());

        return $model->findByCredentials(
            trim($username),
            $password
        );
    }

    public function register(
        string $username,
        string $password,
        string $name,
        string $email
    ): void {
        $username = trim($username);
        $name = trim($name);
        $email = trim($email);

        if (
            $username === '' ||
            $password === '' ||
            $name === '' ||
            $email === ''
        ) {
            throw new InvalidArgumentException(
                'Todos los campos son obligatorios.'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'El correo electrónico no es válido.'
            );
        }

        if (strlen($username) > 50 || mb_strlen($name) > 100) {
            throw new InvalidArgumentException(
                'El usuario o el nombre es demasiado largo.'
            );
        }

        if (strlen($email) > 150) {
            throw new InvalidArgumentException(
                'El correo electrónico es demasiado largo.'
            );
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException(
                'La contraseña debe tener al menos 8 caracteres.'
            );
        }

        $model = new Usuario(Database::connection());

        $model->create(
            $username,
            $password,
            $name,
            $email
        );
    }
}
