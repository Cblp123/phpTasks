<?php
    while ($string = trim(fgets(STDIN))) {
        $array = $string;
        // инициализация переменной для результата
        $res = "";
        // инициализация буфера для хранения числовой части числа
        $buff = "";
        $flag = false;
        $length = strlen($array);
        for ($i = 0; $i < $length; $i++) {
            // считываем символ строки
            $element = $array[$i];
            // если символ явлется кавычкой 
            if ($element === "'") {
                // если буфер явлется число
                if (is_numeric($buff) ) {
                    if ($flag) $res = $res . ($buff * 2) . "'"; // удваиваем буфер
                    else {
                        $res = $res. $buff. "'";
                    }
                }
                else {
                    $res = $res. $buff. "'";
                }
                $buff = "";
                $flag = true;
            }
            else {
                $buff = $buff . $element;
            }
        }
        if ($buff) {
            $res = $res . $buff;
        }
        echo $res. "\n";
    }
?>
