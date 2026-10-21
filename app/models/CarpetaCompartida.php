<?php
namespace MyBox\Models;

use PDO;
use RuntimeException;

final class CarpetaCompartida
{
    public function __construct(private PDO $db)
    {
    }

    public function share(int $folderId, string $owner, string $recipient): void
    {
        $folder = $this->db->prepare('SELECT id FROM carpetas WHERE id = ? AND usuario = ?');
        $folder->execute([$folderId, $owner]);
        if (!$folder->fetch()) {
            throw new RuntimeException('La carpeta no existe o no te pertenece.');
        }
        if ($recipient === $owner) {
            throw new RuntimeException('No puedes compartir una carpeta contigo mismo.');
        }

        $user = $this->db->prepare('SELECT usuario FROM usuarios WHERE usuario = ?');
        $user->execute([$recipient]);
        if (!$user->fetch()) {
            throw new RuntimeException('No existe un usuario con ese nombre.');
        }

        $share = $this->db->prepare(
            'INSERT INTO carpetas_compartidas (carpeta_id, usuario_compartido)
             VALUES (?, ?) ON DUPLICATE KEY UPDATE usuario_compartido = VALUES(usuario_compartido)'
        );
        $share->execute([$folderId, $recipient]);
    }

    public function listForUser(string $username): array
    {
        $query = $this->db->prepare(
            'SELECT c.id, c.nombre, c.usuario AS propietario, c.creada_en
             FROM carpetas_compartidas cc
             INNER JOIN carpetas c ON c.id = cc.carpeta_id
             WHERE cc.usuario_compartido = ?
             ORDER BY c.nombre'
        );
        $query->execute([$username]);
        return $query->fetchAll();
    }
}
