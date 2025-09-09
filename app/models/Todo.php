<?php

namespace App\Models;

use App\Database\DB;

class Todo {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getInstance();
    }

    public function getAllForUser($userId, $isAdmin = false) {
        if ($isAdmin) {
            $stmt = $this->pdo->prepare("SELECT * FROM todos");
            $stmt->execute();
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM todos WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll();
    }

    public function getById($id, $userId, $isAdmin = false) {
        $stmt = $this->pdo->prepare("SELECT * FROM todos WHERE id = ?");
        $stmt->execute([$id]);
        $todo = $stmt->fetch();
        if (!$todo || (!$isAdmin && $todo['user_id'] != $userId)) {
            return null;
        }
        return $todo;
    }

    public function create($userId, $title, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO todos (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $title, $description]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $userId, $title, $description, $completed, $isAdmin = false) {
        $todo = $this->getById($id, $userId, $isAdmin);
        if (!$todo) return false;
        $stmt = $this->pdo->prepare("UPDATE todos SET title = ?, description = ?, completed = ? WHERE id = ?");
        $stmt->execute([$title, $description, $completed, $id]);
        return true;
    }

    public function delete($id, $userId, $isAdmin = false) {
        $todo = $this->getById($id, $userId, $isAdmin);
        if (!$todo) return false;
        $stmt = $this->pdo->prepare("DELETE FROM todos WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    }
}