<?php
namespace MyBox\Models;
use PDO;

final class Carpeta {
    public function __construct(private PDO $db)
    {
    }

    public function owns(int $id, int $userId): bool
    {
        $query = $this->db->prepare(
            'SELECT 1 FROM carpetas WHERE id = ? AND usuario_id = ?'
        );
        $query->execute([$id, $userId]);

        return (bool) $query->fetchColumn();
    }

    public function canRead(int $id, int $userId): bool
    {
        $query = $this->db->prepare(
            'WITH RECURSIVE anc AS (
                SELECT id, carpeta_padre_id, usuario_id
                FROM carpetas
                WHERE id = ?
                UNION ALL
                SELECT parent.id, parent.carpeta_padre_id, parent.usuario_id
                FROM carpetas parent
                JOIN anc child ON child.carpeta_padre_id = parent.id
            )
            SELECT 1
            FROM anc
            WHERE usuario_id = ?
               OR EXISTS (
                    SELECT 1 FROM carpetas_compartidas cc
                    WHERE cc.carpeta_id = anc.id AND cc.usuario_id = ?
               )
            LIMIT 1'
        );
        $query->execute([$id, $userId, $userId]);

        return (bool) $query->fetchColumn();
    }

    public function listForUser(int $userId, ?int $parentId = null): array
    {
        if ($parentId !== null && !$this->canRead($parentId, $userId)) {
            throw new \RuntimeException(
                'No tienes permiso para ver esta carpeta.',
                403
            );
        }

        $sql = 'SELECT c.id, c.nombre, c.usuario_id, c.carpeta_padre_id,
                       c.fecha_creacion, u.usuario AS propietario
                FROM carpetas c
                JOIN usuarios u ON u.id = c.usuario_id
                WHERE ';

        if ($parentId === null) {
            $sql .= 'c.usuario_id = ? AND c.carpeta_padre_id IS NULL';
            $arguments = [$userId];
        } else {
            $sql .= 'c.carpeta_padre_id = ?';
            $arguments = [$parentId];
        }

        $query = $this->db->prepare($sql . ' ORDER BY c.nombre');
        $query->execute($arguments);

        return $query->fetchAll();
    }

    public function create(
        int $userId,
        string $name,
        ?int $parentId = null
    ): int {
        $name = trim($name);

        if ($name === '' || mb_strlen($name) > 100) {
            throw new \InvalidArgumentException(
                'El nombre debe tener entre 1 y 100 caracteres.'
            );
        }

        if ($parentId !== null && !$this->owns($parentId, $userId)) {
            throw new \RuntimeException(
                'Solo puedes crear subcarpetas dentro de tus carpetas.',
                403
            );
        }

        $query = $this->db->prepare(
            'INSERT INTO carpetas (nombre, usuario_id, carpeta_padre_id)
             VALUES (?, ?, ?)'
        );
        $query->execute([$name, $userId, $parentId]);

        return (int) $this->db->lastInsertId();
    }

    public function deleteForUser(int $id, int $userId): bool
    {
        $query = $this->db->prepare(
            'DELETE FROM carpetas WHERE id = ? AND usuario_id = ?'
        );
        $query->execute([$id, $userId]);

        return $query->rowCount() > 0;
    }

    public function sharedRoots(int $userId): array
    {
        $query = $this->db->prepare(
            'SELECT c.id, c.nombre, c.fecha_creacion,
                    u.usuario AS propietario
             FROM carpetas_compartidas cc
             JOIN carpetas c ON c.id = cc.carpeta_id
             JOIN usuarios u ON u.id = c.usuario_id
             WHERE cc.usuario_id = ?
             ORDER BY c.nombre'
        );
        $query->execute([$userId]);

        return $query->fetchAll();
    }

    public function breadcrumbs(int $id, int $userId): array
    {
        if (!$this->canRead($id, $userId)) {
            throw new \RuntimeException(
                'No tienes permiso para ver esta carpeta.',
                403
            );
        }

        $query = $this->db->prepare(
            'WITH RECURSIVE ancestors AS (
                SELECT id, nombre, carpeta_padre_id, 0 AS depth
                FROM carpetas
                WHERE id = ?
                UNION ALL
                SELECT parent.id, parent.nombre, parent.carpeta_padre_id,
                       child.depth + 1
                FROM carpetas parent
                JOIN ancestors child
                  ON child.carpeta_padre_id = parent.id
            )
            SELECT id, nombre, carpeta_padre_id
            FROM ancestors
            ORDER BY depth DESC'
        );
        $query->execute([$id]);

        return $query->fetchAll();
    }

    public function sharingStatus(int $id, int $userId): array
    {
        $query = $this->db->prepare(
            'WITH RECURSIVE ancestors AS (
                SELECT id, carpeta_padre_id
                FROM carpetas
                WHERE id = ?
                UNION ALL
                SELECT parent.id, parent.carpeta_padre_id
                FROM carpetas parent
                JOIN ancestors child
                  ON child.carpeta_padre_id = parent.id
            )
            SELECT
                EXISTS (
                    SELECT 1
                    FROM carpetas_compartidas cc
                    WHERE cc.carpeta_id = ?
                ) AS compartida,
                EXISTS (
                    SELECT 1
                    FROM carpetas_compartidas cc
                    JOIN ancestors a ON a.id = cc.carpeta_id
                    WHERE cc.usuario_id = ?
                ) AS compartida_conmigo'
        );
        $query->execute([$id, $id, $userId]);
        $status = $query->fetch();

        return [
            'compartida' => (bool) ($status['compartida'] ?? false),
            'compartida_conmigo' => (bool) (
                $status['compartida_conmigo'] ?? false
            ),
        ];
    }
}
