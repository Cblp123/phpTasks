<?php

// Подключаем необходимые контроллеры
use App\controllers\AuthController;
use App\controllers\BookController;

// Маршруты для аутентификации
$app->router->get('/login', [AuthController::class, 'login']); // Страница входа (GET)
$app->router->post('/login', [AuthController::class, 'login']); // Обработка входа (POST)
$app->router->get('/register', [AuthController::class, 'register']); // Страница регистрации (GET)
$app->router->post('/register', [AuthController::class, 'register']); // Обработка регистрации (POST)
$app->router->get('/logout', [AuthController::class, 'logout']); // Выход

// Маршруты для работы с книгами
$app->router->get('/', [BookController::class, 'index']); // Главная страница (список книг)
$app->router->get('/books', [BookController::class, 'index']); // Список книг
$app->router->get('/books/create', [BookController::class, 'create']); // Форма добавления книги
$app->router->post('/books/create', [BookController::class, 'create']); // Обработка добавления книги
$app->router->get('/books/edit', [BookController::class, 'edit']); // Форма редактирования книги
$app->router->post('/books/edit', [BookController::class, 'edit']); // Обработка редактирования книги
$app->router->post('/books/delete', [BookController::class, 'delete']); // Удаление книги 