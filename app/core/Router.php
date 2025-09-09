<?php

namespace App\Core;

class Router {
    private static $routes = [];
    private $prefix = '';

    public static function group($prefix, $callback) {
        $router = new self();
        $router->prefix = $prefix;
        $callback($router);
    }

    public function add($method, $path, $handler, $middlewares = []) {
        self::$routes[] = [
            'method' => $method,
            'path' => $this->prefix . $path,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function get($path, $handler, $middlewares = []) {
        $this->add('GET', $path, $handler, $middlewares);
    }

    public function post($path, $handler, $middlewares = []) {
        $this->add('POST', $path, $handler, $middlewares);
    }

    public function put($path, $handler, $middlewares = []) {
        $this->add('PUT', $path, $handler, $middlewares);
    }

    public function delete($path, $handler, $middlewares = []) {
        $this->add('DELETE', $path, $handler, $middlewares);
    }

    public static function dispatch(Request $request) {
        $allowedMethods = [];
        $pathExists = false;

        foreach (self::$routes as $route) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $request->uri, $matches)) {
                $pathExists = true;
                $allowedMethods[] = $route['method'];

                if ($route['method'] === $request->method) {
                    $request->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    $next = function () use ($route, $request) {
                        list($controller, $method) = explode('@', $route['handler']);
                        $controller = "App\\Controllers\\" . $controller;
                        $instance = new $controller();
                        $instance->$method($request);
                    };

                    $middlewares = array_reverse($route['middlewares']);
                    foreach ($middlewares as $middleware) {
                        $middlewareClass = "App\\Middlewares\\" . $middleware;
                        $instance = new $middlewareClass();
                        $next = function () use ($instance, $request, $next) {
                            $instance->handle($request, $next);
                        };
                    }

                    $next();
                    return;
                }
            }
        }

        if ($pathExists) {
            // Path exists but method not allowed
            Response::error(
                'Method Not Allowed: Allowed methods are ' . implode(', ', $allowedMethods),
                405,
                'Method not allowed'
            );
        } else {
            // Path does not exist
            Response::error('Not Found', 404);
        }
    }
}