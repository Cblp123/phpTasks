<?php
$ans = [];
while (($ip = trim(fgets(STDIN)))) {
    // считаем кол-во разделителей
    $colonCount = substr_count($ip, ":");
    // если меньше 7 разделителей, добавляем недостающие блоки нулей
    if ($colonCount < 7) {
        $ip = str_replace("::", str_repeat(":0000", 8 - $colonCount) . ":", $ip);
    }
    // разбиваем IP на части по двоеточию и дополняем недостающие нулями до 4х знаков
    $ipParts = explode(':', $ip);

    $res = "";
    // дополняем части IP нулями до 4х знаков и объединяем части с двоеточиями
    foreach ($ipParts as $ipPart) {
        $res .= str_pad($ipPart, 4, '0', STR_PAD_LEFT) . ":";
    }
    // удаляем последнее двоеточие и выводим результат
    $res = substr($res, 0, -1);
    $ans[] = $res;
}

foreach ($ans as $ip) {
    echo  "$ip\n";
}
?>