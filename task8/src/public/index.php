<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Application;
use App\Core\Database;

$config = [
    'db' => [
        'dsn' => 'mysql:host=mysql;dbname=library',
        'user' => 'user',
        'password' => 'password'
    ]
];

$app = new Application(dirname(__DIR__), $config);

require_once __DIR__ . '/../routes/web.php';

$app->run(); 