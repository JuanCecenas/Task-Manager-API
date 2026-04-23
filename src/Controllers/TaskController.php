<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\TaskService;

class TaskController
{
    private TaskService $service;

    public function __construct()
    {
        $this->service = new TaskService();
    }

    public function index(Request $request, Response $response): void
    {
        $tasks = $this->service->getAll();

        $response->json($tasks);
    }

    public function store(Request $request, Response $response): void
    {
        $data = $request->getBody();

        $task = $this->service->create($data);

        $response->json($task, 201);
    }
}