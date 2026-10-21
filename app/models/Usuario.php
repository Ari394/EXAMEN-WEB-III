
<?php
namespace MyBox\Models;

use PDO;

final class Usuario
{
    public function __construct(private PDO $db)
    {
    }

    // Buscar un usuario por su nombre de usuario.
    public function findByUsername(string $username): ?array
    {
        $query = $this->db->prepare(
            'SELECT id, usuario, nombre, correo, password
             FROM usuarios
             WHERE usuario = ?'
        );

        $query->execute([$username]);

        $user = $query->fetch();

        return $user ?: null;
    }

    // Comprobar las credenciales del usuario.
    public function findByCredentials(
        string $username,
        string $password
    ): ?array {
        $user = $this->findByUsername($username);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        // No devolver el hash de la contraseña.
        unset($user['password']);

        return $user;
    }

    // Registrar un usuario nuevo.
    public function create(
        string $username,
        string $password,
        string $name,
        string $email
    ): void {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $query = $this->db->prepare(
            'INSERT INTO usuarios
             (usuario, password, nombre, correo)
             VALUES (?, ?, ?, ?)'
        );

        $query->execute([
            $username,
            $hashedPassword,
            $name,
            $email
        ]);
    }
}
