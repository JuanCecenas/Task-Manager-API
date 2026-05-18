<?php

namespace App\Core;

class Response
{
    public function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function error(string $message, int $status = 400): void
    {
        $this->json(['error' => $message], $status);
    }

    public function success($data = null, string $message = 'Success', int $status = 200): void
    {
        $response = ['message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->json($response, $status);
    }
}
