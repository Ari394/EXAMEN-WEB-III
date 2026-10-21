<?php
namespace MyBox\Controllers;

use MyBox\Config\Database;
use MyBox\Models\Carpeta;

final class Carpetas
{
    public function index(string $username): array
    {
        return (new Carpeta(Database::connection()))->listForUser($username);
    }

    public function create(string $username, string $name): int
    {
        $name = trim($name);
        if ($name === '' || strlen($name) > 100 || preg_match('/[\/\\\\]/', $name)) {
            throw new \InvalidArgumentException('El nombre debe tener entre 1 y 100 caracteres y no incluir rutas.');
        }
        return (new Carpeta(Database::connection()))->create($username, $name);
    }

    public function delete(int $id, string $username): bool
    {
        return (new Carpeta(Database::connection()))->deleteForUser($id, $username);
    }
}
