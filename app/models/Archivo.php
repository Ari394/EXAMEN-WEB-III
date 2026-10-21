
<?php
namespace MyBox\Models;

use PDO;
use RuntimeException;

final class Archivo
{
    private const MAX_FILE_SIZE = 20 * 1024 * 1024;

    public function __construct(private PDO $db)
    {
    }

    // Listar archivos del usuario en una carpeta.
    public function listForUser(
        int $userId,
        ?int $folderId = null
    ): array {
        $sql = '
            SELECT
                id,
                nombre,
                comentario,
                tipo,
                tamano,
                fecha_subida,
                carpeta_id
            FROM archivos
            WHERE usuario_id = :usuario_id
        ';

        if ($folderId === null) {
            $sql .= ' AND carpeta_id IS NULL';
        } else {
            $sql .= ' AND carpeta_id = :carpeta_id';
        }

        $sql .= ' ORDER BY fecha_subida DESC';

        $query = $this->db->prepare($sql);

        $query->bindValue(
            ':usuario_id',
            $userId,
            PDO::PARAM_INT
        );

        if ($folderId !== null) {
            $query->bindValue(
                ':carpeta_id',
                $folderId,
                PDO::PARAM_INT
            );
        }

        $query->execute();

        return $query->fetchAll();
    }

    // Subir un archivo y guardar su contenido en MySQL.
    public function upload(
        int $userId,
        ?int $folderId,
        array $file,
        string $comment = ''
    ): int {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                'No se pudo recibir el archivo.'
            );
        }

        $size = (int) $file['size'];

        if ($size <= 0 || $size > self::MAX_FILE_SIZE) {
            throw new RuntimeException(
                'El archivo debe tener un tamaño máximo de 20 MB.'
            );
        }

        if (mb_strlen($comment) > 50) {
            throw new RuntimeException(
                'El comentario no puede superar 50 caracteres.'
            );
        }

        $temporaryPath = $file['tmp_name'];

        if (!is_uploaded_file($temporaryPath)) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }

        $content = file_get_contents($temporaryPath);

        if ($content === false) {
            throw new RuntimeException(
                'No se pudo leer el archivo.'
            );
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))
            ->file($temporaryPath);

        $name = basename(
            str_replace('\\', '/', $file['name'])
        );

        $query = $this->db->prepare(
            'INSERT INTO archivos
                (nombre, comentario, tipo, tamano,
                 contenido, usuario_id, carpeta_id)
             VALUES
                (:nombre, :comentario, :tipo, :tamano,
                 :contenido, :usuario_id, :carpeta_id)'
        );

        $query->bindValue(':nombre', $name);
        $query->bindValue(':comentario', $comment);
        $query->bindValue(':tipo', $mime);
        $query->bindValue(
            ':tamano',
            $size,
            PDO::PARAM_INT
        );
        $query->bindValue(
            ':contenido',
            $content,
            PDO::PARAM_LOB
        );
        $query->bindValue(
            ':usuario_id',
            $userId,
            PDO::PARAM_INT
        );

        if ($folderId === null) {
            $query->bindValue(
                ':carpeta_id',
                null,
                PDO::PARAM_NULL
            );
        } else {
            $query->bindValue(
                ':carpeta_id',
                $folderId,
                PDO::PARAM_INT
            );
        }

        $query->execute();

        return (int) $this->db->lastInsertId();
    }

    // Buscar un archivo propio por su ID.
    public function findById(
        int $fileId,
        int $userId
    ): ?array {
        $query = $this->db->prepare(
            'SELECT *
             FROM archivos
             WHERE id = ?
               AND usuario_id = ?'
        );

        $query->execute([$fileId, $userId]);

        $file = $query->fetch();

        return $file ?: null;
    }

    // Eliminar un archivo propio.
    public function delete(
        int $fileId,
        int $userId
    ): bool {
        $query = $this->db->prepare(
            'DELETE FROM archivos
             WHERE id = ?
               AND usuario_id = ?'
        );

        $query->execute([$fileId, $userId]);

        return $query->rowCount() > 0;
    }
}
