<?php

namespace App\Core;

class Request
{
    // Получение пути из URL
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    // Получение метода запроса
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    // Проверка, является ли запрос GET
    public function isGet()
    {
        return $this->method() === 'get';
    }

    // Проверка, является ли запрос POST
    public function isPost()
    {
        return $this->method() === 'post';
    }

    // Получение тела запроса
    public function getBody()
    {
        $body = [];
        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

    // Получение GET-параметров
    public function getQueryParams()
    {
        return $_GET;
    }
} 