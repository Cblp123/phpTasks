<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Application;

$config = [
    'db' => [
        'dsn' => 'mysql:host=mysql;dbname=library',
        'user' => 'user',
        'password' => 'password'
    ]
];

$app = new Application(__DIR__, $config);
$app->db->applyMigrations(); 