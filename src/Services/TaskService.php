<?php

namespace App\Services;

use App\Repositories\Contracts\TaskRepositoryInterface;
use Exception;

class TaskService
{
    private TaskRepositoryInterface $repository;

    public function __construct(TaskRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(int $id): array
    {
        $task = $this->repository->getById($id);
        if (!$task) {
            throw new Exception("Task not found", 404);
        }
        return $task;
    }

    public function create(array $data): array
    {
        if (empty($data['title'])) {
            throw new Exception("Title is required", 422);
        }

        $title = trim($data['title']);
        if (strlen($title) === 0) {
            throw new Exception("Title cannot be empty", 422);
        }

        return $this->repository->create([
            'title' => $title,
            'status' => $data['status'] ?? 'pending',
        ]);
    }

    public function update(int $id, array $data): array
    {
        $this->getById($id);

        if (isset($data['title'])) {
            $title = trim($data['title']);
            if (strlen($title) === 0) {
                throw new Exception("Title cannot be empty", 422);
            }
            $data['title'] = $title;
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $this->getById($id);
        return $this->repository->delete($id);
    }
}
