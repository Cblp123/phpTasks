<?php

namespace App\Core;

class Session
{
    // Ключ для flash-сообщений
    protected const FLASH_KEY = 'flash_messages';

    // Конструктор: инициализация сессии и flash-сообщений
    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    // Установка flash-сообщения
    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    // Получение flash-сообщения
    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    // Установка значения в сессию
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // Получение значения из сессии
    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    // Удаление значения из сессии
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    // Деструктор: удаляет flash-сообщения, помеченные на удаление
    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
} 