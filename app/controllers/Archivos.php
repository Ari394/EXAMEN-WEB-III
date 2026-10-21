
<?php
namespace MyBox\Controllers;

use MyBox\Config\Database;
use MyBox\Models\Archivo;
use InvalidArgumentException;

final class Archivos
{
    private Archivo $model;

    public function __construct()
    {
        $this->model = new Archivo(
            Database::connection()
        );
    }

    // Mostrar los archivos propios de una carpeta.
    public function listForUser(
        int $userId,
        ?int $folderId = null
    ): array {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                'El usuario no es válido.'
            );
        }

        return $this->model->listForUser(
            $userId,
            $folderId
        );
    }

    // Subir un archivo a MySQL.
    public function upload(
        int $userId,
        ?int $folderId,
        array $file,
        string $comment = ''
    ): int {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                'El usuario no es válido.'
            );
        }

        if ($folderId !== null && $folderId <= 0) {
            throw new InvalidArgumentException(
                'La carpeta no es válida.'
            );
        }

        return $this->model->upload(
            $userId,
            $folderId,
            $file,
            $comment
        );
    }

 
    public function findById(
        int $fileId,
        int $userId
    ): ?array {
        if ($fileId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException(
                'El archivo o usuario no es válido.'
            );
        }

        return $this->model->findById(
            $fileId,
            $userId
        );
    }

    .
    public function delete(
        int $fileId,
        int $userId
    ): bool {
        if ($fileId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException(
                'El archivo o usuario no es válido.'
            );
        }

        return $this->model->delete(
            $fileId,
            $userId
        );
    }
}
