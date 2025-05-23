<?php
    function send_mail($to, $subject, $message) : void {
        $smtp_server = "smtp.mail.ru";
        $smtp_port = 465;
        $smtp_user = "alexandro-0@mail.ru";
        $smtp_pass = getenv('smtp_pass');
        
        // При помощи заголовка сервер распознает от кого письмо, кому отправлять ответ, какой формат письма
        $headers = "From: alexandro-0@mail.ru\r\n";
        $headers .= "Reply-To: alexandro-0@mail.ru\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
        // Открываем соединение с SMTP-сервером
        $socket = fsockopen("ssl://$smtp_server", $smtp_port, $errno, $errstr, 30);
        
        // Устанавливаем связь с сервером
        fputs($socket, "EHLO $smtp_server\r\n");
        fgets($socket, 512);
    
        // Отправляем запрос на логин
        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket, 512);

        // авторизуемся
        fputs($socket, base64_encode($smtp_user) . "\r\n");
        fgets($socket, 512);
        fputs($socket, base64_encode($smtp_pass) . "\r\n");
        fgets($socket, 512);
    
        // От кого письмо
        fputs($socket, "MAIL FROM: <$smtp_user>\r\n");
        fgets($socket, 512);
    
        // Кому письмо
        fputs($socket, "RCPT TO: <$to>\r\n");
        fgets($socket, 512);

        // Сообщаем серверу, что готовы начать отправку письма
        fputs($socket, "DATA\r\n");
        fgets($socket, 512);
    
        fputs($socket, "Subject: $subject\r\n");
        fputs($socket, "$headers\r\n");
        fputs($socket, "$message\r\n");
        fputs($socket, ".\r\n"); // Завершение письма для SMPT-сервера
        fgets($socket, 512);

        // Закрываем соединение
        fputs($socket, "QUIT\r\n");
        fclose($socket);
    }

    // подключаем файл подключения к базе данных 
    require 'db.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // считывание даных с post-запроса
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $comment = trim($_POST['comment'] ?? '');

        // Расчёт времени связи
        $sendTime = date("H:i:s d.m.Y", time() + 3 * 90 * 60); 
        
        // Проверка на повторную заявку
        $stmt = $pdo->prepare("SELECT created FROM users WHERE email = ? ORDER BY created DESC LIMIT 1");
        $stmt->execute([$email]);
        $lastEntry = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($lastEntry && strtotime($lastEntry['created']) > (time() -  60 * 60)) {
            $nextAttempt = date("H:i:s d.m.Y", strtotime($lastEntry['created'] . " +4 hour"));
            $message = "Вы уже оставили заявку. Повторная заявка возможна после: $nextAttempt";
            echo $message;
            exit;
        }
    
        // Сохранение заявки в БД
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $comment]);
        
        // Разобьем поле name на фамилия имя отчество
        $surname = explode(' ', $name)[0] ?? '';
        $name = explode(' ', $name)[1] ?? '';
        $lastname = explode(' ', $name)[2] ?? '';
        // Составляем сообщение
        $message = "
            Оставлено сообщение из формы обратной связи!<br>
            <strong>Фамилия:</strong> $surname<br>
            <strong>Имя:</strong> $name<br>
            <strong>Отчество:</strong> $lastname<br>
            <strong>E-mail:</strong> $email<br>
            <strong>Телефон:</strong> $phone<br>
            С вами свяжутся после <strong>$sendTime</strong>.
        ";
        // Отправка письма
        $subject = 'Новая заявка';
        $body = strip_tags($message);
        send_mail("alexandro-0@mail.ru", $subject, $body);
        
        echo $message;
    }
    ?>