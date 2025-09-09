<?php

use App\Database\DB;

function up() {
    $pdo = DB::getInstance();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS todos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            completed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
}

function down() {
    $pdo = DB::getInstance();
    $pdo->exec("DROP TABLE IF EXISTS todos");
}