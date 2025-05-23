<?php

function restoreUrl($malformedUrl) {
    // Определяем протокол (http или https)
    if (strpos($malformedUrl, 'https') === 0) {
        $protocol = 'https';
        $remaining = substr($malformedUrl, 5);
    } else {
        $protocol = 'http';
        $remaining = substr($malformedUrl, 4);
    }

    // Находим домен (до .ru или .com)
    $domain = '';
    $context = '';

    if (($ruPos = strpos($remaining, 'ru')) !== false) {
        $domain = substr($remaining, 0, $ruPos);
        $tld = 'ru';
        $context = substr($remaining, $ruPos + 2);
    } elseif (($comPos = strpos($remaining, 'com')) !== false) {
        $domain = substr($remaining, 0, $comPos);
        $tld = 'com';
        $context = substr($remaining, $comPos + 3);
    }

    // Собираем восстановленный URL
    $restoredUrl = $protocol . '://' . $domain . '.' . $tld;
    
    // Добавляем путь, если он существует
    if (!empty($context)) {
        $restoredUrl .= '/' . $context;
    }

    return $restoredUrl;
}

// Читаем входные данные из dataA.txt
$malformedUrl = trim(file_get_contents('dataA.txt'));

// Обрабатываем и восстанавливаем URL
$restoredUrl = restoreUrl($malformedUrl);

// Выводим результат в консоль
echo $restoredUrl;
