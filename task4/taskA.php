<?php

$arr = [];
while ($input = fgets(STDIN)) {
    // считываем данные
    $input = trim($input); 
    if (strlen($input) < 8) break;
    $string = explode("    ", $input); 

    $id = $string[0]; 
    // создаем дату для дальнейшего сравнения
    $date = DateTime::createFromFormat('d.m.Y H:i:s', $string[1]);
    // сравниваем дату и добавляем новую или увеличиваем счетчик для текущего идентификатора
    if (isset($arr[$id])) {
        if (DateTime::createFromFormat('d.m.Y H:i:s', $arr[$id]["date"]) < $date) {
            // для дальнейшего вывода сохраним дату в виде строки
            $arr[$id]["date"] = $string[1];
        }
        $arr[$id]["count"] += 1;
    }
    else {
        $arr[$id]["date"] = $string[1];
        $arr[$id]["count"] = 1;
    }
}
 // выводим результаты
foreach ($arr as $key => $value) {
    echo $value["count"] . " " . $key . " " . $value["date"] . PHP_EOL; 
}
?>