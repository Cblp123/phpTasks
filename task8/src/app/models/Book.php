<?php

namespace App\models;

use App\Core\Model;
use App\Core\Application;

class Book extends Model
{
    // Свойства модели книги
    public ?int $id = null;
    public string $title = '';
    public string $author = '';
    public string $cover_image = '';
    public string $book_file = '';
    public string $read_date = '';
    public bool $allow_download = false;
    public ?string $created_at = null;
    public ?int $user_id = null;

    // Имя таблицы в базе данных
    public static function tableName(): string
    {
        return 'books';
    }

    // Сохранение книги в базу данных
    public function save()
    {
        $tableName = $this->tableName();
        $attributes = ['title', 'author', 'cover_image', 'book_file', 'read_date', 'allow_download', 'user_id'];
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(",", $attributes) . ") 
                VALUES (" . implode(",", $params) . ")");
        
        // Установим текущего пользователя
        $this->user_id = Application::$app->session->get('user');
        
        foreach ($attributes as $attribute) {
            if ($attribute === 'allow_download') {
                $statement->bindValue(":$attribute", $this->allow_download ? 1 : 0, \PDO::PARAM_INT);
            } else {
                $statement->bindValue(":$attribute", $this->{$attribute});
            }
        }

        return $statement->execute();
    }

    // Обновление информации о книге
    public function update($id)
    {
        // Проверяем, принадлежит ли книга текущему пользователю
        $currentBook = self::findOne(['id' => $id]);
        if (!$currentBook || $currentBook->user_id !== Application::$app->session->get('user')) {
            return false;
        }

        $tableName = $this->tableName();
        $attributes = ['title', 'author', 'cover_image', 'book_file', 'read_date', 'allow_download'];
        $params = array_map(fn($attr) => "$attr = :$attr", $attributes);
        
        $sql = "UPDATE $tableName SET " . implode(", ", $params) . " WHERE id = :id";
        $statement = self::prepare($sql);
        
        foreach ($attributes as $attribute) {
            if ($attribute === 'allow_download') {
                // Явно устанавливаем значение allow_download на основе наличия параметра в форме
                $this->allow_download = isset($_POST['allow_download']);
                $statement->bindValue(":$attribute", $this->allow_download ? 1 : 0, \PDO::PARAM_INT);
            } else {
                $statement->bindValue(":$attribute", $this->{$attribute});
            }
        }
        $statement->bindValue(":id", $id, \PDO::PARAM_INT);

        return $statement->execute();
    }

    // Проверка, может ли пользователь скачать книгу
    public function canDownload()
    {
        $currentUserId = Application::$app->session->get('user');
        return !Application::isGuest() && ($this->user_id === $currentUserId || $this->allow_download);
    }

    // Поиск одной книги по условию
    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    // Получение всех книг
    public static function findAll()
    {
        $tableName = static::tableName();
        $statement = self::prepare("SELECT * FROM $tableName ORDER BY read_date DESC");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    // Удаление книги
    public static function delete($id)
    {
        // Проверяем, принадлежит ли книга текущему пользователю
        $book = self::findOne(['id' => $id]);
        if (!$book || $book->user_id !== Application::$app->session->get('user')) {
            return false;
        }

        $tableName = static::tableName();
        $statement = self::prepare("DELETE FROM $tableName WHERE id = :id");
        $statement->bindValue(":id", $id);
        return $statement->execute();
    }

    // Правила валидации для формы книги
    public function rules(): array
    {
        return [
            'title' => [self::RULE_REQUIRED],
            'author' => [self::RULE_REQUIRED],
            'read_date' => [self::RULE_REQUIRED]
        ];
    }

    // Подготовка SQL-запроса
    public static function prepare($sql)
    {
        return Application::$app->db->prepare($sql);
    }

    // Загрузка данных из массива в свойства модели
    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'allow_download') {
                    $this->allow_download = isset($data['allow_download']) && $data['allow_download'] !== '0';
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }
} 