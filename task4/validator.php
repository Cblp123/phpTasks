<?php
    // Запрашиваем номер задания у пользователя
    $dir = readline("task: ");
    // Формируем путь к тестовым файлам
    $path = "тесты/$dir";
    // Формируем имя PHP скрипта для проверки
    $php_script = "task$dir.php";

    // Перебираем все .dat файлы в директории с тестами
    foreach (glob("$path/*.dat") as $dat_file) {
        // Формируем имя файла с ответами, заменяя расширение .dat на .ans
        $ans_file = str_replace(".dat", ".ans", $dat_file);

        // Запускаем PHP скрипт с входными данными из .dat файла
        $output = shell_exec("php $php_script < $dat_file");
        // Получаем ожидаемый результат из .ans файла
        $result = file_get_contents($ans_file);

        // Для заданий A и B (не C) выполняем простое сравнение строк
        if ($dir != "C") {
            // Удаляем все переносы строк и табуляции для корректного сравнения
            $output = str_replace(array("\r", "\n", "\t"), '', $output);
            $result = str_replace(array("\r", "\n", "\t"), '', $result);
    
            if ($output == $result) {
                echo basename($dat_file). " OK\n";
            } else {
                echo basename($dat_file). " FAIL\n";
            }
        } else {
            // Для задания C выполняем специальную проверку с учетом погрешности
            // Разбиваем вывод и ожидаемый результат на строки
            $output = explode("\n", $output);
            $result = explode("\n", $result);
            $flag = true;

            // Проверяем каждую строку
            for ($i = 0; $i < count($output) - 1; $i += 1) {
                // Разбиваем строки на числа
                $output = explode(" ", $output[$i]);
                $result = explode(" ", $result[$i]);

                // Проверяем, что разница между числами не превышает 0.01
                if (abs($output[1] - $result[1]) > 0.01) {
                    $flag = false;
                    break;
                }
            }

            if ($flag) {
                echo basename($dat_file) . " OK\n";
            } else {
                echo basename($dat_file) . " FAIL\n";
            }
        }
    }
?>
