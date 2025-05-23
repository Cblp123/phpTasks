<?php

class m0003_update_existing_books
{
    public function up()
    {
        $db = \App\Core\Application::$app->db;
        // Получаем ID первого пользователя в системе
        $result = $db->pdo->query("SELECT id FROM users ORDER BY id LIMIT 1");
        $userId = $result->fetchColumn();
        
        if ($userId) {
            // Обновляем все книги без user_id
            $SQL = "UPDATE books SET user_id = :user_id WHERE user_id IS NULL";
            $statement = $db->pdo->prepare($SQL);
            $statement->execute(['user_id' => $userId]);
        }
    }

    public function down()
    {
        $db = \App\Core\Application::$app->db;
        $SQL = "UPDATE books SET user_id = NULL";
        $db->pdo->exec($SQL);
    }
} 