<?php
namespace MyBox\Controllers;

use MyBox\Models\Archivo;

final class Archivos
{
    public function listForUser(string $username): array
    {
        $root = getenv('HOME_PATH');
        if ($root === false || $root === '') {
            throw new \RuntimeException('La variable HOME_PATH no está configurada.');
        }
        return (new Archivo($root))->listForUser($username);
    }
}
