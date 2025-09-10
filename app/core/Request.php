<?php

namespace App\Core;

class Request
{
    public $method;     // HTTP method (GET, POST, etc.)
    public $uri;       // Request URI (e.g., /v1/register)
    public $query = []; // Query parameters (?key=value)
    public $params = []; // Route parameters (e.g., {id} in /v1/todos/{id})
    public $body = [];  // Request body (JSON)
    public $headers = []; // HTTP headers
    public $user = null; // User data set by AuthMiddleware

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $this->uri = preg_replace('#^/public/#', '/', $uri);
        $this->uri = '/' . trim($this->uri, '/');

        $this->headers = $this->getHeaders();

        $contentType = $this->getHeader('Content-Type') ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $rawBody = file_get_contents('php://input');
            $this->body = $rawBody ? json_decode($rawBody, true) ?? [] : [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->body = [];
            }
        } else {
            $this->body = [];
        }

        $this->query = $_GET ?? [];
        $this->params = [];
    }

    private function getHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strncmp($key, 'HTTP_', 5) === 0) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    public function getHeader($name)
    {
        $name = str_replace(' ', '-', ucwords(strtolower(str_replace(['-', '_'], ' ', $name))));
        return $this->headers[$name] ?? null;
    }
}
