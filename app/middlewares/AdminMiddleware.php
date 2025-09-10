<?php

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

class AdminMiddleware extends Middleware {
    public function handle(Request $request, $next) {
        if ($request->user['role'] !== 'admin') {
            Response::send(false, null, 'Authorization failed', 'Forbidden: Admin access required', 403);
        }
        $next();
    }
}