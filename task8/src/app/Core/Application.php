<?php

namespace App\Core;

class Application
{
    // Глобальные свойства приложения
    public static string $ROOT_DIR;
    public static Application $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?Controller $controller = null;
    public string $layout = 'main';

    // Конструктор приложения
    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
    }

    // Запуск приложения
    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            echo $this->router->renderView('_error', [
                'exception' => $e
            ]);
        }
    }

    // Получение текущего контроллера
    public function getController(): ?Controller
    {
        return $this->controller;
    }

    // Установка текущего контроллера
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    // Проверка, гость ли пользователь
    public static function isGuest(): bool
    {
        return !self::$app->session->get('user');
    }
} 