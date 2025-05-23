<?php
// Функция для нормализации XML-документов
function normalize_xml($xml) {
    // Удаляем все XML-декларации кроме первой
    $xml = preg_replace('/<\?xml[^>]+\?>\s*/', '', $xml);
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . $xml;
    
    // Инициализируем DOMDocument для работы с XML
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = true;
    @$dom->loadXML($xml);
    
    // Приводим XML к единому формату: удаляем лишние пробелы и переносы строк
    $xmlString = $dom->saveXML();
    $xmlString = str_replace(["\r", "\n"], '', $xmlString);
    $xmlString = preg_replace('/>\s+</', '><', $xmlString);
    return trim($xmlString);
}

// Запрашиваем номер задания у пользователя
$dir = readline("task: ");
// Формируем путь к тестовым файлам
$path = "тесты/$dir";
// Формируем имя PHP скрипта для проверки
$php_script = "task$dir.php";

// Специальная обработка для задания B (работа с XML)
if ($dir == "B") {
    // Считываем все файлы с разделами и товарами
    foreach (glob("$path/*_sections.xml") as $sections_file) {
        // Получаем префикс имени файла
        $prefix = str_replace('_sections.xml', '', basename($sections_file));
        // Формируем пути к файлам с товарами и результатами
        $products_file = "$path/{$prefix}_products.xml";
        $result_file = "$path/{$prefix}_result.xml";
        
        // Запускаем скрипт с передачей имен файлов
        $output = shell_exec("php $php_script $sections_file $products_file 2>&1");
        
        // Считываем ожидаемый результат
        $expected = file_get_contents($result_file);
        
        try {
            // Нормализуем оба XML-документа для корректного сравнения
            $output_normalized = normalize_xml($output);
            $expected_normalized = normalize_xml($expected);
            
            if ($output_normalized === $expected_normalized) {
                echo "$prefix OK\n";
            } else {
                echo "$prefix FAIL\n";
                // Сохраняем результаты для отладки
                file_put_contents("{$prefix}_output.xml", $output);
                file_put_contents("{$prefix}_expected.xml", $expected);
            }
        } catch (Exception $e) {
            echo "$prefix ERROR: " . $e->getMessage() . "\n";
        }
    }
    exit();
}

// Обработка остальных заданий (A и C)
// Считываем все файлы с тестами
foreach (glob("$path/*.dat") as $dat_file) {
    // Формируем имя файла с ответами
    $ans_file = str_replace(".dat", ".ans", $dat_file);

    // Запускаем скрипт с входными данными
    $output = shell_exec("php $php_script < $dat_file");
    // Получаем ожидаемый результат
    $result = file_get_contents($ans_file);
    
    // Для задания A выполняем простое сравнение строк
    if ($dir != "C") {
        // Удаляем все переносы строк и табуляции
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
            $output_line = explode(" ", $output[$i]);
            $result_line = explode(" ", $result[$i]);

            // Проверяем, что разница между числами не превышает 0.01
            if (abs($output_line[1] - $result_line[1]) > 0.01) {
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