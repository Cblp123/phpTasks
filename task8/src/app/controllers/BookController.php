<?php

namespace App\controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Application;
use App\Core\middlewares\AuthMiddleware;
use App\models\Book;

/**
 * Контроллер для управления книгами в библиотеке
 * Обеспечивает функционал CRUD для книг и управление файлами
 */
class BookController extends Controller
{
    /**
     * Конструктор контроллера
     * Регистрирует middleware для защиты маршрутов, требующих авторизации
     */
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create', 'edit', 'delete']));
    }

    /**
     * Отображает список всех книг
     */
    public function index()
    {
        $books = Book::findAll();
        return $this->render('books/index', [
            'books' => $books,
            'isGuest' => Application::isGuest()
        ]);
    }

    /**
     * Создание новой книги
     * Обрабатывает загрузку обложки и файла книги
     */
    public function create(Request $request)
    {
        $book = new Book();

        if ($request->isPost()) {
            $book->loadData($request->getBody());

            // Обработка загрузки изображения обложки
            $coverImage = $_FILES['cover_image'] ?? null;
            $bookFile = $_FILES['book_file'] ?? null;

            if ($coverImage && $coverImage['size'] > 0) {
                $book->cover_image = $this->saveFile($coverImage, 'covers');
            }

            // Обработка загрузки файла книги
            if ($bookFile && $bookFile['size'] > 0) {
                if ($bookFile['size'] > 5 * 1024 * 1024) { // 5MB
                    $book->addError('book_file', 'Размер файла должен быть меньше 5МБ');
                } else {
                    $book->book_file = $this->saveFile($bookFile, 'books');
                }
            }

            if ($book->validate() && $book->save()) {
                Application::$app->session->setFlash('success', 'Книга успешно добавлена');
                Application::$app->response->redirect('/');
                return;
            }
        }

        return $this->render('books/create', [
            'model' => $book
        ]);
    }

    /**
     * Редактирование существующей книги
     */
    public function edit(Request $request)
    {
        // Получение ID книги из запроса
        $id = $request->getBody()['id'] ?? null;
        if (!$id) {
            Application::$app->response->redirect('/books');
            return;
        }

        // Проверка прав доступа к редактированию
        $book = Book::findOne(['id' => $id]);
        $currentUserId = Application::$app->session->get('user');
        
        if (!$book || $book->user_id != $currentUserId) {
            Application::$app->response->redirect('/books');
            return;
        }

        if ($request->isPost()) {
            $oldCoverImage = $book->cover_image;
            $oldBookFile = $book->book_file;
            
            $book->loadData($request->getBody());
            
            // Обработка изображения обложки
            if (isset($request->getBody()['remove_cover']) && $oldCoverImage) {
                $this->deleteFile($oldCoverImage);
                $book->cover_image = '';
            } else {
                $coverImage = $_FILES['cover_image'] ?? null;
                if ($coverImage && $coverImage['size'] > 0) {
                    $this->deleteFile($oldCoverImage);
                    $book->cover_image = $this->saveFile($coverImage, 'covers');
                } else {
                    $book->cover_image = $oldCoverImage;
                }
            }

            // Обработка файла книги
            $bookFile = $_FILES['book_file'] ?? null;
            if ($bookFile && $bookFile['size'] > 0) {
                if ($bookFile['size'] > 5 * 1024 * 1024) {
                    $book->addError('book_file', 'Размер файла должен быть меньше 5МБ');
                } else {
                    $this->deleteFile($oldBookFile);
                    $book->book_file = $this->saveFile($bookFile, 'books');
                }
            } else {
                $book->book_file = $oldBookFile;
            }

            if ($book->validate() && $book->update($id)) {
                Application::$app->session->setFlash('success', 'Книга успешно обновлена');
                Application::$app->response->redirect('/books');
                return;
            }
        }

        return $this->render('books/edit', [
            'model' => $book
        ]);
    }

    /**
     * Удаление книги и связанных файлов
     */
    public function delete(Request $request, Response $response)
    {
        $id = $request->getBody()['id'] ?? null;
        if ($id) {
            $book = Book::findOne(['id' => $id]);
            if ($book && $book->user_id === Application::$app->session->get('user')) {
                // Удаление файлов книги
                $filesDeleted = true;
                
                if ($book->cover_image) {
                    $filesDeleted = $filesDeleted && $this->deleteFile($book->cover_image);
                }
                
                if ($book->book_file) {
                    $filesDeleted = $filesDeleted && $this->deleteFile($book->book_file);
                }
                
                if ($filesDeleted && Book::delete($id)) {
                    Application::$app->session->setFlash('success', 'Книга успешно удалена');
                } else {
                    Application::$app->session->setFlash('error', 'Ошибка при удалении книги');
                }
            } else {
                Application::$app->session->setFlash('error', 'У вас нет прав на удаление этой книги');
            }
        }
        $response->redirect('/');
    }

    /**
     * Сохранение загруженного файла
     */
    private function saveFile($file, $directory)
    {
        if (!$file) {
            return '';
        }

        // Получаем ID пользователя
        $userId = Application::$app->session->get('user');
        
        // Формируем структуру директорий
        $readDate = $_POST['read_date'] ?? date('Y-m-d');
        $dateFolder = str_replace('-', '_', $readDate);
        $relativePath = "$directory/$userId/$dateFolder";
        $uploadsPath = Application::$ROOT_DIR . "/public/uploads/$relativePath";

        // Создаем директории
        if (!is_dir($uploadsPath)) {
            mkdir($uploadsPath, 0777, true);
        }

        // Генерируем уникальное имя файла
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid(more_entropy: true) . '.' . $extension;
        $fullPath = "$uploadsPath/$fileName";
        
        move_uploaded_file($file['tmp_name'], $fullPath);
        return "$relativePath/$fileName";
    }

    /**
     * Удаление файла и пустых директорий

     */
    private function deleteFile($filePath)
    {
        if (!$filePath) {
            return true;
        }

        try {
            $fullPath = Application::$ROOT_DIR . "/public/uploads/$filePath";
            
            if (!file_exists($fullPath)) {
                return true;
            }

            if (!unlink($fullPath)) {
                error_log("Ошибка при удалении файла: $fullPath");
                return false;
            }

            // Очистка пустых директорий
            $dateDir = dirname($fullPath);
            $userDir = dirname($dateDir);
            $typeDir = dirname($userDir);

            foreach ([$dateDir, $userDir, $typeDir] as $dir) {
                if (is_dir($dir) && $this->isDirEmpty($dir)) {
                    rmdir($dir);
                }
            }

            return true;
        } catch (\Exception $e) {
            error_log("Исключение при удалении файла: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Проверка, пуста ли директория
     */
    private function isDirEmpty($dir)
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }
} 