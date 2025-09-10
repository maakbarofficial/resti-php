# RESTI PHP - Build REST APIs using Core PHP

A lightweight REST API framework and TODO application backend built in core PHP, mimicking Express.js-style routing. The API supports user authentication with JSON Web Tokens (JWT), role-based authorization (user and admin roles), and CRUD operations for TODO items. It uses a MySQL database with PDO and includes migrations for easy setup.

## Features

- **Versioned API Routes**: All endpoints are prefixed with `/v1` (e.g., `/v1/register`, `/v1/todos`).
- **JWT Authentication**: Custom JWT implementation for secure user authentication.
- **Role-Based Authorization**: Supports `user` and `admin` roles. Admins can view all TODOs, while users can only manage their own.
- **Standardized Responses**: Consistent JSON response format:
  - Success: `{"success": true, "message": "...", "data": {}, "error": null}`
  - Error: `{"success": false, "message": "", "data": null, "error": "..."}`
- **Middleware**: Includes `AuthMiddleware` for JWT verification and `AdminMiddleware` for admin-only routes.
- **Database**: MySQL with PDO, with migrations for `users` and `todos` tables.
- **Routing**: Express.js-like router with support for GET, POST, PUT, DELETE methods.
- **Environment**: Runs on any PHP server.

## Project Structure

```
resti-php/
├── app/
│   ├── config/
│   │   └── database.php        # Database configuration
│   ├── controllers/
│   │   ├── AuthController.php  # Handles register/login
│   │   └── TodoController.php  # Handles TODO CRUD
│   ├── core/
│   │   ├── JWT.php             # Custom JWT implementation
│   │   ├── Middleware.php      # Base middleware class
│   │   ├── Request.php         # HTTP request parser
│   │   ├── Response.php        # Standardized response handler
│   │   └── Router.php          # Route dispatcher
│   ├── database/
│   │   └── DB.php              # PDO database connection
│   ├── middlewares/
│   │   ├── AuthMiddleware.php  # JWT authentication
│   │   └── AdminMiddleware.php # Admin role check
│   ├── migrations/
│   │   ├── 001_create_users_table.php
│   │   └── 002_create_todos_table.php
│   ├── models/
│   │   ├── User.php            # User model (auth)
│   │   └── Todo.php            # Todo model (CRUD)
│   └── routes/
│       └── api.php             # Route definitions
├── public/
│   ├── index.php               # Entry point
│   └── .htaccess               # Apache routing
├── migrate.php                 # Migration script
├── composer.json               # Composer configuration
└── vendor/                     # Composer autoloader
```

## Requirements

- PHP >= 7.4
- MySQL 5.7 or 8.0+
- Laragon/XAMPP/MAMP or any Apache/PHP setup
- Composer
- Postman or cURL for testing

## Setup Instructions

Follow these steps to set up and run the project on your local machine using any PHP Server we will use an example of Laragon here.

### 1. Clone or Set Up the Project
- Place the project in Laragon’s web root: `C:\laragon\www\resti-php`.
- Alternatively, clone from your repository (if hosted):
  ```bash
  git clone https://github.com/maakbarofficial/resti-php.git
  ```

### 2. Install Dependencies
- Navigate to the project directory e.g.:
  ```bash
  cd C:\laragon\www\resti-php
  ```
- Install Composer dependencies (autoloader only, no external packages):
  ```bash
  composer update
  ```

### 3. Configure the Database
- Start Laragon’s MySQL server from the Laragon control panel.
- Open phpMyAdmin (`http://localhost/phpmyadmin`) or use the MySQL CLI:
  ```bash
  mysql -u root -p
  ```
  (Default password is empty in Laragon.)
- Create the `todo_app` database:
  ```sql
  CREATE DATABASE todo_app;
  ```
- Verify the database configuration in `app/config/database.php`:
  ```php
  return [
      'host' => 'localhost',
      'dbname' => 'todo_app',
      'user' => 'root',
      'password' => '',
      'charset' => 'utf8mb4',
  ];
  ```

### 4. Run Migrations
- Run the migration script to create `users` and `todos` tables:
  ```bash
  composer migrate
  ```
  This executes `migrate.php`, creating the necessary tables.

### 5. Use PHP Built-in Server
If you prefer not to use Apache:
- Start the PHP built-in server:
  ```bash
  composer start
  ```
  This runs `php -S localhost:8000 -t public`.

### 7. Test the API
Use Postman or cURL to test the API endpoints at `http://localhost:8000` (PHP server).

#### Register a User
```bash
curl -X POST http://resti.test/v1/register -H "Content-Type: application/json" -d '{"email":"user@example.com","password":"password123","role":"user"}'
```
**Response** (201):
```json
{
    "success": true,
    "message": "User created successfully",
    "data": {"id": 1},
    "error": null
}
```

#### Login
```bash
curl -X POST http://resti.test/v1/login -H "Content-Type: application/json" -d '{"email":"user@example.com","password":"password123"}'
```
**Response** (200):
```json
{
    "success": true,
    "message": "Login successful",
    "data": {"token": "eyJ..."},
    "error": null
}
```

#### Create a TODO
```bash
curl -X POST http://resti.test/v1/todos -H "Authorization: Bearer <jwt>" -H "Content-Type: application/json" -d '{"title":"My Todo","description":"Do something"}'
```
**Response** (201):
```json
{
    "success": true,
    "message": "Todo created successfully",
    "data": {"id": 1},
    "error": null
}
```

#### Get All TODOs (Admin)
- Register an admin user (`role: admin`), login to get a JWT, then:
```bash
curl -X GET http://resti.test/v1/todos -H "Authorization: Bearer <admin_jwt>"
```
**Response** (200):
```json
{
    "success": true,
    "message": "Todos retrieved successfully",
    "data": [
        {"id":1,"user_id":1,"title":"My Todo","description":"Do something","completed":false,"created_at":"2025-09-10 18:14:00"}
    ],
    "error": null
}
```

## Endpoints

| Method | Endpoint            | Description                     | Middleware         |
|--------|---------------------|---------------------------------|-------------------|
| POST   | `/v1/register`      | Register a new user             | None              |
| POST   | `/v1/login`         | Login and get JWT              | None              |
| GET    | `/v1/todos`         | Get user’s or all TODOs (admin) | AuthMiddleware    |
| GET    | `/v1/todos/{id}`    | Get a specific TODO            | AuthMiddleware    |
| POST   | `/v1/todos`         | Create a TODO                  | AuthMiddleware    |
| PUT    | `/v1/todos/{id}`    | Update a TODO                  | AuthMiddleware    |
| DELETE | `/v1/todos/{id}`    | Delete a TODO                  | AuthMiddleware    |
| GET    | `/v1/admin/todos`   | Get all TODOs (admin only)     | AuthMiddleware, AdminMiddleware |

## Security Notes
- **JWT Secret**: Ensure the secret in `app/core/JWT.php`, `app/models/User.php`, and `app/middlewares/AuthMiddleware.php` is secure (e.g., generate with `php -r "echo base64_encode(random_bytes(32));"`).
- **Admin Creation**: In production, restrict `role: admin` in `/v1/register` by hardcoding `$role = 'user'` in `AuthController`.
- **Input Validation**: Add validation for email, password, and TODO fields in controllers.

Thats it. Lets Build Lightweight APIs Now...!