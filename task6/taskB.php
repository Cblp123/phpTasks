<?php
// Получаем пути к входным файлам из аргументов командной строки
// файл с разделами (sections.xml)
$sectionsFile = $argv[1];
// файл с товарами (products.xml)
$productsFile = $argv[2];

// Загружаем XML-файлы с помощью SimpleXML
$sectionsXml = simplexml_load_file($sectionsFile); // Загружаем разделы

$productsXml = simplexml_load_file($productsFile); // Загружаем товары

// Создаем новый XML-документ для результата
// Начинаем с XML-декларации и корневого элемента <ЭлементыКаталога>
$outputXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ЭлементыКаталога></ЭлементыКаталога>');
// Добавляем узел <Разделы> внутрь корневого элемента
$sectionsNode = $outputXml->addChild('Разделы');

// Обрабатываем каждый раздел из исходного файла
foreach ($sectionsXml->Раздел as $section) {
    // Получаем ID и название раздела
    $sectionId = (string)$section->Ид;
    $sectionName = (string)$section->Наименование;
    
    // Создаем узел для текущего раздела в выходном XML
    $outputSection = $sectionsNode->addChild('Раздел');
    $outputSection->addChild('Ид', $sectionId); // Добавляем ID раздела
    $outputSection->addChild('Наименование', $sectionName); // Добавляем название
    // Создаем узел для товаров раздела
    $productsNode = $outputSection->addChild('Товары');
    
    //  ищем товары, что относятся к текущему разделу
    if ($productsXml->Товар) {
        foreach ($productsXml->Товар as $product) {
            // Проверяем все разделы, к которым принадлежит товар
            foreach ($product->Разделы->ИдРаздела as $productSectionId) {
                // Если ID раздела совпадает
                if ((string)$productSectionId == $sectionId) {
                    // Добавляем товар в раздел
                    $outputProduct = $productsNode->addChild('Товар');
                    $outputProduct->addChild('Ид', (string)$product->Ид); // ID товара
                    $outputProduct->addChild('Наименование', (string)$product->Наименование); // Название
                    $outputProduct->addChild('Артикул', (string)$product->Артикул); // Артикул
                    break; // Товар может быть в нескольких разделах, но добавляем один раз
                }
            }
        }
    }
}

// Преобразуем SimpleXML в DOMDocument для красивого форматирования
$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = true; // Сохраняем пробелы
$dom->formatOutput = true;       // Включаем автоформатирование
$dom->loadXML($outputXml->asXML()); // Загружаем XML

// Обрабатываем итоговый XML
$xmlString = $dom->saveXML();
// Удаляем лишние XML-декларации 
$xmlString = preg_replace('/<\?xml[^>]+\?>\s*/', '', $xmlString, 1);
// Выводим результат: сначала XML-декларация, затем содержимое
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . trim($xmlString);
?>