<?php
namespace MyBox\Controllers;

use MyBox\Config\Database;
use MyBox\Models\Usuario;

final class Usuarios
{
    public function authenticate(string $username, string $password): ?array
    {
        return (new Usuario(Database::connection()))->findByCredentials($username, $password);
    }

    public function register(string $username, string $password, string $name, string $email): void
    {
        (new Usuario(Database::connection()))->create($username, $password, $name, $email);
    }
}
