<?php

class m0002_add_user_id_to_books
{
    public function up()
    {
        $db = \App\Core\Application::$app->db;
        $SQL = "ALTER TABLE books ADD COLUMN user_id INT,
                ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
        $db->pdo->exec($SQL);
    }

    public function down()
    {
        $db = \App\Core\Application::$app->db;
        $SQL = "ALTER TABLE books DROP FOREIGN KEY books_ibfk_1;
                ALTER TABLE books DROP COLUMN user_id";
        $db->pdo->exec($SQL);
    }
} 