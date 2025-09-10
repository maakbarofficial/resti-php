<?php

namespace App\Models;

use App\Database\DB;
use App\Core\JWT;

class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DB::getInstance();
    }

    public function create($email, $password, $role = 'user')
    {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashed, $role]);
        return $this->pdo->lastInsertId();
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function generateJWT($user)
    {
        $secret = 'your_secure_random_key_here'; // Same secret as in AuthMiddleware
        $jwt = new JWT($secret);
        $payload = [
            'iss' => 'todo_app',
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
            'sub' => $user['id'],
            'role' => $user['role'],
        ];
        return $jwt->encode($payload);
    }
}
