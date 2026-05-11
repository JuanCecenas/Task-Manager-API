<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Controllers\TaskController;

$request = new Request();
$response = new Response();
$router = new Router();

$router->get('/', function ($req, $res) {
    $res->json([
        'message' => 'Task Manager API',
        'endpoints' => [
            'GET /health' => 'Health check',
            'GET /tasks' => 'List tasks',
            'POST /tasks' => 'Create task (JSON body: {"title":"..."})',
        ],
    ]);
});

// Test route
$router->get('/health', function ($req, $res) {
    $res->json([
        'status' => 'ok',
        'message' => 'API is running'
    ]);
});

$router->get('/tasks', [TaskController::class, 'index']);
$router->post('/tasks', [TaskController::class, 'store']);

$router->resolve($request, $response);