<?php

namespace App\Core;

class Request
{
    public $method;     // HTTP method (GET, POST, etc.)
    public $uri;       // Request URI (e.g., /v1/register)
    public $params = []; // Query parameters or route parameters
    public $body = [];  // JSON body for POST/PUT requests
    public $headers = []; // HTTP headers
    public $user = null; // User data set by AuthMiddleware

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->headers = getallheaders();
        $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->params = $_GET; // For query params, but we'll handle route params in router
    }
}
