<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Controllers\TaskController;

$request = new Request();
$response = new Response();
$router = new Router();

$router->middleware(function ($req, $res) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');

    if ($req->getMethod() === 'OPTIONS') {
        $res->json([], 204);
        exit;
    }
});

$router->get('/', function ($req, $res) {
    $res->json([
        'message' => 'Task Manager API',
        'version' => '2.0',
        'endpoints' => [
            'GET /health' => 'Health check',
            'GET /tasks' => 'List all tasks',
            'GET /tasks/{id}' => 'Get task by ID',
            'POST /tasks' => 'Create task (JSON body: {"title":"..."})',
            'PUT /tasks/{id}' => 'Update task',
            'PATCH /tasks/{id}' => 'Partially update task',
            'DELETE /tasks/{id}' => 'Delete task',
        ],
    ]);
});

$router->get('/health', function ($req, $res) {
    $res->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
});

$router->get('/tasks', [TaskController::class, 'index']);
$router->get('/tasks/{id}', [TaskController::class, 'show']);
$router->post('/tasks', [TaskController::class, 'store']);
$router->put('/tasks/{id}', [TaskController::class, 'update']);
$router->patch('/tasks/{id}', [TaskController::class, 'update']);
$router->delete('/tasks/{id}', [TaskController::class, 'delete']);

$router->resolve($request, $response);
