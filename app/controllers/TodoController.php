<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Todo;

class TodoController {
    public function index(Request $request) {
        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $todos = $todoModel->getAllForUser($request->user['id'], $isAdmin);
        Response::success($todos, 'Todos retrieved successfully');
    }

    public function show(Request $request) {
        $id = $request->params['id'] ?? null;
        if (!$id) {
            Response::error('Missing todo ID', 400);
        }

        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $todo = $todoModel->getById($id, $request->user['id'], $isAdmin);
        if (!$todo) {
            Response::error('Todo not found or unauthorized', 404);
        }

        Response::success($todo, 'Todo retrieved successfully');
    }

    public function store(Request $request) {
        $title = $request->body['title'] ?? null;
        $description = $request->body['description'] ?? '';

        if (!$title) {
            Response::error('Missing title', 400);
        }

        $todoModel = new Todo();
        $id = $todoModel->create($request->user['id'], $title, $description);
        Response::success(['id' => $id], 'Todo created successfully', 201);
    }

    public function update(Request $request) {
        $id = $request->params['id'] ?? null;
        $title = $request->body['title'] ?? null;
        $description = $request->body['description'] ?? null;
        $completed = $request->body['completed'] ?? false;

        if (!$id || !$title) {
            Response::error('Missing id or title', 400);
        }

        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $success = $todoModel->update($id, $request->user['id'], $title, $description, $completed, $isAdmin);
        if (!$success) {
            Response::error('Todo not found or unauthorized', 404);
        }

        Response::success(null, 'Todo updated successfully');
    }

    public function destroy(Request $request) {
        $id = $request->params['id'] ?? null;
        if (!$id) {
            Response::error('Missing todo ID', 400);
        }

        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $success = $todoModel->delete($id, $request->user['id'], $isAdmin);
        if (!$success) {
            Response::error('Todo not found or unauthorized', 404);
        }

        Response::success(null, 'Todo deleted successfully');
    }
}