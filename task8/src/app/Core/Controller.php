<?php

namespace App\Core;

class Controller
{
    // Имя используемого шаблона
    public string $layout = 'main';
    // Текущее действие
    public string $action = '';
    // Массив middleware
    protected array $middlewares = [];

    // Рендеринг представления
    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }

    // Установка шаблона
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    // Регистрация middleware
    public function registerMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    // Получение всех middleware
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
} 