<?php

require __DIR__ . '/vendor/autoload.php';

$direction = $argv[1] ?? 'up';
$migrationFiles = glob(__DIR__ . '/app/migrations/*.php');

foreach ($migrationFiles as $file) {
    require $file;
    if ($direction === 'up') {
        up();
        echo "Migrated up: " . basename($file) . PHP_EOL;
    } elseif ($direction === 'down') {
        down();
        echo "Migrated down: " . basename($file) . PHP_EOL;
    }
}