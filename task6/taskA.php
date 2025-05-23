<?php
$n = (int)trim(fgets(STDIN));
for ($i = 0; $i < $n; $i++) {
    $input = trim(fgets(STDIN));
    $parts = explode(" ", $input);
    
    // Обрабатываем даты 
    $dateStr1 = str_replace('_', ' ', $parts[0]);
    $zone1 = (int)$parts[1]; 
    $dateStr2 = str_replace('_', ' ', $parts[2]);
    $zone2 = (int)$parts[3]; 
    
    // Создаём объекты DateTime
    $date1 = DateTime::createFromFormat('d.m.Y H:i:s', $dateStr1);
    $date2 = DateTime::createFromFormat('d.m.Y H:i:s', $dateStr2);
    
    // Получаем Unix-время (секунды с 1970-01-01 UTC)
    $timestamp1 = $date1->getTimestamp();
    $timestamp2 = $date2->getTimestamp();
    
    // Учитываем разницу часовых поясов (переводим в UTC)
    $utcTime1 = $timestamp1 - ($zone1 * 3600); // Вычитаем пояс вылета
    $utcTime2 = $timestamp2 - ($zone2 * 3600); // Вычитаем пояс прибытия
    
    // Разница в секундах
    $flightTime = $utcTime2 - $utcTime1;
    
    echo $flightTime . PHP_EOL;
}
?>