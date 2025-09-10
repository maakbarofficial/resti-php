<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/routes/api.php';
use App\Core\Request;
use App\Core\Router;
$request = new Request();
Router::dispatch($request);