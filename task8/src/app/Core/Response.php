<?php

namespace App\Core;

class Response
{
    // Конструктор: запускает буферизацию вывода, если она не запущена
    public function __construct()
    {
        if (ob_get_level() === 0) {
            ob_start();
        }
    }

    // Установка HTTP-кода ответа
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    // Перенаправление на другой URL
    public function redirect($url)
    {
        // Очистка существующего вывода
        if (ob_get_length() > 0) {
            ob_clean();
        }
        header('Location: ' . $url);
        exit;
    }
} 