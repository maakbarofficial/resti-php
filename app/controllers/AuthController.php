<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class AuthController {
    public function register(Request $request) {
        $email = $request->body['email'] ?? null;
        $password = $request->body['password'] ?? null;
        $role = $request->body['role'] ?? 'user'; // Allow admin creation for demo, secure in prod

        if (!$email || !$password) {
            Response::error('Missing email or password', 400);
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            Response::error('Email already exists', 400);
        }

        $id = $userModel->create($email, $password, $role);
        Response::success(['id' => $id], 'User created successfully', 201);
    }

    public function login(Request $request) {
        $email = $request->body['email'] ?? null;
        $password = $request->body['password'] ?? null;

        if (!$email || !$password) {
            Response::error('Missing email or password', 400);
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            Response::error('Invalid credentials', 401);
        }

        $token = $userModel->generateJWT($user);
        Response::success(['token' => $token], 'Login successful');
    }
}