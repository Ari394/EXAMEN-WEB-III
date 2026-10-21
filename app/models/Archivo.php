<?php
namespace MyBox\Models;

final class Archivo
{
    public function __construct(private string $root)
    {
    }

    public function listForUser(string $username): array
    {
        $directory = $this->userDirectory($username);
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        foreach (new \DirectoryIterator($directory) as $entry) {
            if ($entry->isDot()) {
                continue;
            }
            $path = $entry->getPathname();
            $files[] = [
                'nombre' => $entry->getFilename(),
                'tamano' => $entry->getSize(),
                'ultimo_acceso' => $entry->getATime(),
                'archivo' => $entry->isFile(),
                'directorio' => $entry->isDir(),
                'lectura' => is_readable($path),
                'escritura' => is_writable($path),
                'ejecutable' => is_executable($path),
            ];
        }
        return $files;
    }

    public function userDirectory(string $username): string
    {
        return rtrim($this->root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $username;
    }
}
