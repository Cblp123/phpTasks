<?php

namespace App\controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Application;
use App\models\User;

class AuthController extends Controller
{
    // Метод для входа пользователя
    public function login(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $user = new User();
            $user->loadData($request->getBody());
            
            // Ищем пользователя по email
            $userDb = User::findOne(['email' => $user->email]);
            if ($userDb && password_verify($user->password, $userDb->password)) {
                // Сохраняем пользователя в сессии и перенаправляем на главную
                Application::$app->session->set('user', $userDb->id);
                $response->redirect('/');
                return;
            }
            
            // Если данные неверны, возвращаем ошибку
            return $this->render('login', [
                'model' => $user,
                'errors' => ['email' => ['Неверные учетные данные']]
            ]);
        }
        // Если GET-запрос, просто отображаем форму
        return $this->render('login');
    }

    // Метод для регистрации пользователя
    public function register(Request $request)
    {
        $user = new User();

        if ($request->isPost()) {
            $user->loadData($request->getBody());

            // Валидация и сохранение пользователя
            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash('success', 'Спасибо за регистрацию');
                Application::$app->response->redirect('/');
                return;
            }

            // Если есть ошибки, возвращаем форму с ошибками
            return $this->render('register', [
                'model' => $user
            ]);
        }
        // Если GET-запрос, просто отображаем форму
        return $this->render('register', [
            'model' => $user
        ]);
    }

    // Метод для выхода пользователя
    public function logout(Request $request, Response $response)
    {
        Application::$app->session->remove('user'); // Удаляем пользователя из сессии
        $response->redirect('/'); // Перенаправляем на главную
    }
}