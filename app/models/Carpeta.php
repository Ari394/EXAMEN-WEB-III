<?php
namespace MyBox\Models;

use PDO;

final class Carpeta
{
    public function __construct(private PDO $db)
    {
    }

    public function listForUser(string $username): array
    {
        $query = $this->db->prepare(
            'SELECT id, nombre, usuario, creada_en FROM carpetas WHERE usuario = ? ORDER BY nombre'
        );
        $query->execute([$username]);
        return $query->fetchAll();
    }

    public function create(string $username, string $name): int
    {
        $query = $this->db->prepare('INSERT INTO carpetas (usuario, nombre) VALUES (?, ?)');
        $query->execute([$username, $name]);
        return (int) $this->db->lastInsertId();
    }

    public function deleteForUser(int $id, string $username): bool
    {
        $this->db->beginTransaction();
        try {
            $shares = $this->db->prepare(
                'DELETE FROM carpetas_compartidas WHERE carpeta_id IN
                 (SELECT id FROM carpetas WHERE id = ? AND usuario = ?)'
            );
            $shares->execute([$id, $username]);

            $folder = $this->db->prepare('DELETE FROM carpetas WHERE id = ? AND usuario = ?');
            $folder->execute([$id, $username]);
            $deleted = $folder->rowCount() === 1;
            $this->db->commit();
            return $deleted;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }
}
