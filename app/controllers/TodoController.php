<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Todo;

class TodoController
{
    public function index(Request $request)
    {
        $validator = new Validator();
        $rules = [
            'completed' => ['boolean']
        ];

        if (!$validator->validate($request->query, $rules)) {
            Response::send(false, null, 'Validation failed', implode('; ', array_merge(...array_values($validator->getErrors()))), 400);
        }

        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $todos = $todoModel->getAllForUser($request->user['id'], $isAdmin);
        Response::send(true, $todos, 'Todos retrieved successfully', null);
    }

    public function show(Request $request)
    {
        $validator = new Validator();
        $rules = [
            'id' => ['required', 'integer']
        ];

        if (!$validator->validate($request->params, $rules)) {
            Response::send(false, null, 'Validation failed', implode('; ', array_merge(...array_values($validator->getErrors()))), 400);
        }

        $id = $request->params['id'];
        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $todo = $todoModel->getById($id, $request->user['id'], $isAdmin);
        if (!$todo) {
            Response::send(false, null, 'Operation failed', 'Todo not found or unauthorized', 404);
        }

        Response::send(true, $todo, 'Todo retrieved successfully', null);
    }

    public function store(Request $request)
    {
        $validator = new Validator();
        $rules = [
            'title' => ['required', 'max:255'],
            'description' => ['max:1000']
        ];

        if (!$validator->validate($request->body, $rules)) {
            Response::send(false, null, 'Validation failed', implode('; ', array_merge(...array_values($validator->getErrors()))), 400);
        }

        $title = $request->body['title'];
        $description = $request->body['description'] ?? '';

        $todoModel = new Todo();
        $id = $todoModel->create($request->user['id'], $title, $description);
        Response::send(true, ['id' => $id], 'Todo created successfully', null, 201);
    }

    public function update(Request $request)
    {
        $validator = new Validator();
        $rules = [
            'id' => ['required', 'integer'],
            'title' => ['required', 'max:255'],
            'description' => ['max:1000'],
            'completed' => ['boolean']
        ];

        $data = array_merge($request->params, $request->body);
        if (!$validator->validate($data, $rules)) {
            Response::send(false, null, 'Validation failed', implode('; ', array_merge(...array_values($validator->getErrors()))), 400);
        }

        $id = $request->params['id'];
        $title = $request->body['title'];
        $description = $request->body['description'] ?? null;
        $completed = $request->body['completed'] ?? null;

        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $success = $todoModel->update($id, $request->user['id'], $title, $description, $completed ?? false, $isAdmin);
        if (!$success) {
            Response::send(false, null, 'Operation failed', 'Todo not found or unauthorized', 404);
        }

        Response::send(true, null, 'Todo updated successfully', null);
    }

    public function destroy(Request $request)
    {
        $validator = new Validator();
        $rules = [
            'id' => ['required', 'integer']
        ];

        if (!$validator->validate($request->params, $rules)) {
            Response::send(false, null, 'Validation failed', implode('; ', array_merge(...array_values($validator->getErrors()))), 400);
        }

        $id = $request->params['id'];
        $todoModel = new Todo();
        $isAdmin = $request->user['role'] === 'admin';
        $success = $todoModel->delete($id, $request->user['id'], $isAdmin);
        if (!$success) {
            Response::send(false, null, 'Operation failed', 'Todo not found or unauthorized', 404);
        }

        Response::send(true, null, 'Todo deleted successfully', null);
    }
}
