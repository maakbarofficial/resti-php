<?php

use App\Core\Router;

Router::group('/v1', function (Router $router) {
    // Auth routes (no middleware)
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

    // Todo routes (with auth)
    $router->get('/todos', 'TodoController@index', ['AuthMiddleware']);
    $router->get('/todos/{id}', 'TodoController@show', ['AuthMiddleware']);
    $router->post('/todos', 'TodoController@store', ['AuthMiddleware']);
    $router->put('/todos/{id}', 'TodoController@update', ['AuthMiddleware']);
    $router->delete('/todos/{id}', 'TodoController@destroy', ['AuthMiddleware']);

    $router->get('/admin/todos', 'TodoController@index', ['AuthMiddleware', 'AdminMiddleware']);
});