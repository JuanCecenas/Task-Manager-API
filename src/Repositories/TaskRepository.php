<?php

namespace App\Repositories;

use App\Core\Database;
use App\Repositories\Contracts\TaskRepositoryInterface;
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
        return $stmt->fetchAll();
    }

    public function create(array $task): array
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tasks (title, status)
            VALUES (:title, :status)
        ");

        $stmt->execute([
            'title' => $task['title'],
            'status' => $task['status']
        ]);

        $task['id'] = $this->pdo->lastInsertId();

        return $task;
    }
}