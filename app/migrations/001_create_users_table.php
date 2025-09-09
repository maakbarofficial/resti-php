<?php

use App\Database\DB;

function up() {
    $pdo = DB::getInstance();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

function down() {
    $pdo = DB::getInstance();
    $pdo->exec("DROP TABLE IF EXISTS users");
}