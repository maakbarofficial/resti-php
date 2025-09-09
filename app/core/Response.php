<?php

namespace App\Core;

class Response {
    public static function success($data = null, $message = 'Operation successful', $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'error' => null
        ]);
        exit;
    }

    public static function error($error, $status = 400, $message = '') {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => $error
        ]);
        exit;
    }
}