<?php
namespace MyBox\Models;

use PDO;

final class Usuario
{
    public function __construct(private PDO $db)
    {
    }

    public function findByCredentials(string $username, string $password): ?array
    {
        $query = $this->db->prepare(
            'SELECT nombre, usuario FROM usuarios WHERE usuario = ? AND contra = MD5(?)'
        );
        $query->execute([$username, $password]);
        $user = $query->fetch();
        return $user ?: null;
    }

    public function create(string $username, string $password, string $name, string $email): void
    {
        $query = $this->db->prepare(
            'INSERT INTO usuarios (usuario, contra, nombre, email) VALUES (?, MD5(?), ?, ?)'
        );
        $query->execute([$username, $password, $name, $email]);
    }
}
