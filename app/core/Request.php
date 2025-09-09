<?php

namespace App\Core;

class Request {
    public $method;
    public $uri;
    public $params = [];
    public $body = [];
    public $headers = [];
    public $user = null; // Will be set by middleware

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->headers = getallheaders();
        $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->params = $_GET; // For query params, but we'll handle route params in router
    }
}