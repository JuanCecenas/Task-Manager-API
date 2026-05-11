<?php

namespace App\Services;

use App\Repositories\TaskRepository;

class TaskService
{

    private TaskRepository $repository;

    public function __construct()
    {
        $this->repository = new TaskRepository();
    }

    private array $tasks = [];

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function create(array $data): array
    {
        if (empty($data['title'])) {
            throw new \Exception("Title is required");
        }

        $task = [
            'id' => count($this->tasks) + 1,
            'title' => $data['title'],
            'status' => 'pending'
        ];

        $this->repository->create($task);

        return $task;
    }

    public function update(int $id, array $data): array
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}