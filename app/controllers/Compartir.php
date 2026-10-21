<?php
namespace MyBox\Controllers;

use MyBox\Config\Database;
use MyBox\Models\CarpetaCompartida;

final class Compartir
{
    public function share(int $folderId, string $owner, string $recipient): void
    {
        $recipient = trim($recipient);
        if ($recipient === '' || strlen($recipient) > 15) {
            throw new \InvalidArgumentException('Indica un nombre de usuario válido.');
        }
        (new CarpetaCompartida(Database::connection()))->share($folderId, $owner, $recipient);
    }

    public function sharedWith(string $username): array
    {
        return (new CarpetaCompartida(Database::connection()))->listForUser($username);
    }
}
