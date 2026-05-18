<?php

namespace App\Repositories;

use App\Core\Database;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Exception;
use PDO;

class TaskRepository implements TaskRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM tasks ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $task): array
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tasks (title, status, created_at, updated_at)
            VALUES (:title, :status, NOW(), NOW())
        ");

        $stmt->execute([
            'title' => $task['title'],
            'status' => $task['status'] ?? 'pending',
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->getById($id);
    }

    public function update(int $id, array $data): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            throw new Exception("Task not found");
        }

        $title = $data['title'] ?? $existing['title'];
        $status = $data['status'] ?? $existing['status'];

        $stmt = $this->pdo->prepare("
            UPDATE tasks 
            SET title = :title, status = :status, updated_at = NOW() 
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'status' => $status,
        ]);

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
