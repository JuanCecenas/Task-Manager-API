<?php

namespace App\Services;

class TaskService
{
    private array $tasks = [];

    public function getAll(): array
    {
        return $this->tasks;
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

        $this->tasks[] = $task;

        return $task;
    }
}