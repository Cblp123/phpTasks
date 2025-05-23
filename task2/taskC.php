<?php
// функция проверки длины входящей строки
function validateS($value, $n, $m) {
   echo (($n <= strlen($value)) and (strlen($value) <= $m)) ? "OK\n" : "FAIL\n";
}
// функция, является ли входящая строка числом
function validateN($value, $n, $m) {
    echo ((preg_match('/^-?\d+$/', $value)) and ($n <= $value) and ($value <= $m)) ? "OK\n" : "FAIL\n";
}
// функция проверки номера телефона по шаблону +7 (999) 999-99-99
function validateP($value) { 
    echo ((preg_match('/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', $value))) ? "OK\n" : "FAIL\n";
}
// функция проверки даты/времени dd.mm.yyyy HH:MM
function validateD($value) {
    preg_match("/^(\d{1,2})\.(\d{1,2})\.(\d{4})\s(\d{1,2}):(\d{2})$/", $value, $matches);
    // проверка на паттерн
    if (empty($matches)) {
        echo "FAIL\n";
        return;
    }
    $dd = $matches[1];
    $mm = $matches[2];
    $yy = $matches[3];

    $hh = $matches[4];
    $mi = $matches[5];
    // проверка даты и времени
    if ((checkdate($mm, $dd, $yy)) and (($hh < 24) and ($mi < 60))) {
        echo "OK\n";
    }
    else {
        echo "FAIL\n";
    }
}
// проверка шаблона почты
function validateE($value) {
    echo ((preg_match('/^[A-Za-z0-9][A-Za-z0-9_]{3,29}@[A-Za-z]{2,30}\.[a-z]{2,10}$/', $value))) ? "OK\n" : "FAIL\n";
}

while ($line = trim(fgets(STDIN))) {
    // разбор строки на аргументы и тип операции
    preg_match('/^<(.*)> (.*)$/', $line, $matches);
    $value =  $matches[1];
    if (strlen($matches[2]) > 1 ) {
        list($type, $n, $m) = explode(" ", $matches[2]);
    } else {
        $type = $matches[2];
    }
    // выполнение операций
    switch ($type) {
        case 'S':
            validateS($value, $n, $m);
            break;
        case 'N':
            validateN($value, $n, $m);
            break;
        case 'P':
            validateP($value);
            break;
        case 'D':
            validateD($value);
            break;
        case 'E':
            validateE($value);
            break;
    }
}
?>