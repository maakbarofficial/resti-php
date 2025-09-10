<?php

namespace App\Core;

class Response
{
    public static function send($success, $data = null, $message = '', $error = null, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'error' => $error
        ]);
        exit;
    }
}
