<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\TaskService;
use Exception;

class TaskController
{
    private TaskService $service;

    public function __construct()
    {
        $this->service = new TaskService(new \App\Repositories\TaskRepository());
    }

    public function index(Request $request, Response $response): void
    {
        try {
            $tasks = $this->service->getAll();
            $response->json($tasks);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function show(Request $request, Response $response): void
    {
        try {
            $id = (int) $request->getAttribute('id');
            $task = $this->service->getById($id);
            $response->json($task);
        } catch (Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->error($e->getMessage(), $status);
        }
    }

    public function store(Request $request, Response $response): void
    {
        try {
            $data = $request->getBody();
            $task = $this->service->create($data);
            $response->json($task, 201);
        } catch (Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->error($e->getMessage(), $status);
        }
    }

    public function update(Request $request, Response $response): void
    {
        try {
            $data = $request->getBody();
            $id = (int) $request->getAttribute('id');
            $task = $this->service->update($id, $data);
            $response->json($task);
        } catch (Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->error($e->getMessage(), $status);
        }
    }

    public function delete(Request $request, Response $response): void
    {
        try {
            $id = (int) $request->getAttribute('id');
            $deleted = $this->service->delete($id);
            $response->json(['deleted' => $deleted]);
        } catch (Exception $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->error($e->getMessage(), $status);
        }
    }
}
