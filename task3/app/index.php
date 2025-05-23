<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Форма обратной связи</title>
</head>
<body>
    <h1>Форма обратной связи</h1>
    <p id="message" style="text-align: center;">
            <?php 
            if (isset($_SESSION['message'])) {
                if (is_array($_SESSION['message'])) {
                    echo implode("<br>", $_SESSION['message']);
                } else {
                    echo $_SESSION['message'];
                }
                unset($_SESSION['message']);
            }
            ?>
    </p>
    <form id="feedbackForm">

        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Почта:</label>
        <input type="text" id="email" name="email" placeholder="example@email.com"><br><br>

        <label for="phone">Номер телефона:</label>
        <input type="tel" id="phone" name="phone" placeholder="89005553535"><br><br>
        
        <label for="comment">Комментарий:</label>
        <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>

        <input type="submit" value="Записаться на приём">
    </form>

    <script src="script.js"></script>
</body>
</html>