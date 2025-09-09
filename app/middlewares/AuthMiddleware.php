<?php

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware extends Middleware {
    public function handle(Request $request, $next) {
        $authHeader = $request->headers['Authorization'] ?? null;
        if (!$authHeader || !preg_match('/Bearer (.+)/', $authHeader, $matches)) {
            Response::error('Unauthorized: Missing or invalid token', 401);
        }

        $token = $matches[1];
        $secret = 'your_secure_random_key_here'; // Match User.php

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $userModel = new User();
            $user = $userModel->findById($decoded->sub);
            if (!$user) {
                Response::error('Unauthorized: Invalid token', 401);
            }
            $request->user = $user;
            $next();
        } catch (\Exception $e) {
            Response::error('Unauthorized: Invalid token', 401);
        }
    }
}