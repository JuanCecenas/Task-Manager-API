<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$request = new Request();
$response = new Response();
$router = new Router();

// Test route
$router->get('/health', function ($req, $res) {
    $res->json([
        'status' => 'ok',
        'message' => 'API is running'
    ]);
});

$router->resolve($request, $response);