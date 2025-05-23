<?php
// Функция для преобразования IP-адреса в целое число
function ipToInt($ip) {
    $parts = explode('.', $ip);
    return ($parts[0] << 24) | ($parts[1] << 16) | ($parts[2] << 8) | $parts[3];
}

// Функция для преобразования целого числа обратно в IP-адрес
function intToIp($int) {
    return sprintf('%d.%d.%d.%d', 
        ($int >> 24) & 255,
        ($int >> 16) & 255,
        ($int >> 8) & 255,
        $int & 255
    );
}

// Функция для поиска маски сети по массиву IP-адресов
function findNetworkMask($ips, $k) {
    if (count($ips) == 0) return "0.0.0.0";
    
    $min = $ips[0];
    $max = $ips[0];
    // Находим минимальный и максимальный IP в массиве
    foreach ($ips as $ip) {
        if ($ip < $min) $min = $ip;
        if ($ip > $max) $max = $ip;
    }
    
    // Вычисляем разницу между min и max
    $diff = $min ^ $max;
    $mask = 0xFFFFFFFF;
    // Сдвигаем маску, пока diff & mask не станет 0
    while (($diff & $mask) != 0) {
        $mask <<= 1;
    }
    
    return intToIp($mask);
}

// Чтение входных данных
$input = file('dataB.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$firstLine = explode(' ', $input[0]);
$n = (int)$firstLine[0];
$k = (int)$firstLine[1];

$ips = array();
// Преобразуем IP-адреса в числа
for ($i = 1; $i <= $n; $i++) {
    $ips[] = ipToInt($input[$i]);
}

// Находим маску подсети
$mask = findNetworkMask($ips, $k);

// Вывод результата
echo $mask . "\n";
?>