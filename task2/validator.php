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

        // Сравниваем полученный результат с ожидаемым
        if ($output == $result) {
            echo basename($dat_file). " OK\n";
        } else {
            echo basename($dat_file). " FAIL\n";
        }
    }
?>
