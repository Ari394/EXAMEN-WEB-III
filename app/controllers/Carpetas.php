<?php
namespace MyBox\Controllers;
use MyBox\Config\Database;
use MyBox\Models\Carpeta;

final class Carpetas {
    private function model(): Carpeta
    {
        return new Carpeta(Database::connection());
    }

    public function index(int $userId, ?int $parentId = null): array
    {
        return $this->model()->listForUser($userId, $parentId);
    }

    public function owns(int $folderId, int $userId): bool
    {
        return $this->model()->owns($folderId, $userId);
    }

    public function find(int $folderId, int $userId): ?array
    {
        return $this->model()->findOwned($folderId, $userId);
    }

    public function create(
        int $userId,
        string $name,
        ?int $parentId = null
    ): int {
        return $this->model()->create($userId, $name, $parentId);
    }

    public function delete(int $id, int $userId): bool
    {
        return $this->model()->deleteForUser($id, $userId);
    }

    public function sharedWith(int $userId): array
    {
        return $this->model()->sharedRoots($userId);
    }

    public function breadcrumbs(int $folderId, int $userId): array
    {
        return $this->model()->breadcrumbs($folderId, $userId);
    }

    public function sharingStatus(int $folderId, int $userId): array
    {
        return $this->model()->sharingStatus($folderId, $userId);
    }
}
