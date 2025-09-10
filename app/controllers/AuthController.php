<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\User;

class AuthController
{
    public function register(Request $request)
    {
        $validator = new Validator();
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'role' => ['in:user,admin']
        ];

        if (!$validator->validate($request->body, $rules)) {
            Response::send(false, null, 'Validation failed', implode('; ', array_merge(...array_values($validator->getErrors()))), 400);
        }

        $email = $request->body['email'];
        $password = $request->body['password'];
        $role = $request->body['role'] ?? 'user';

        // Restrict admin role in production
        if ($role === 'admin') {
            Response::send(false, null, 'Operation failed', 'Admin role creation is restricted', 403);
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            Response::send(false, null, 'Operation failed', 'Email already exists', 400);
        }

        $id = $userModel->create($email, $password, $role);
        Response::send(true, ['id' => $id], 'User created successfully', null, 201);
    }

    public function login(Request $request)
    {
        $validator = new Validator();
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required']
        ];

        if (!$validator->validate($request->body, $rules)) {
            Response::send(false, null, 'Validation failed', implode('; ', array_merge(...array_values($validator->getErrors()))), 400);
        }

        $email = $request->body['email'];
        $password = $request->body['password'];

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            Response::send(false, null, 'Authentication failed', 'Invalid credentials', 401);
        }

        $token = $userModel->generateJWT($user);
        Response::send(true, ['token' => $token], 'Login successful', null);
    }
}
