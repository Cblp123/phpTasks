<?php
// Устанавливаем заголовок для возвращаемого содержимого как JSON
header("Content-Type: application/json");

// Получаем API ключ из переменных окружения
$apiKey = getenv("YANDEX_API_KEY");

// Проверяем наличие и непустое значение параметра address в GET запросе
if (!isset($_GET['address']) || empty(trim($_GET['address']))) {
    echo json_encode(["error" => "Не найден address"]);
    exit;
}

// Кодируем адрес для URL и устанавливаем параметры языка и формата ответа
$address = urlencode($_GET['address']);
$lang = "ru_RU"; 
$format = "json"; 

// Формируем URL для запроса к Яндекс Геокодеру
$url = "https://geocode-maps.yandex.ru/1.x/?apikey=$apiKey&geocode=$address&lang=$lang&format=$format";
// Отправляем запрос (используем @ для подавления ошибок)
$response = @file_get_contents($url);

// Проверяем успешность запроса
if ($response === false) {
    echo json_encode(["error" => "Ошибка подключения к API Яндекс.Карт (адрес)"]);
    exit;
}

// Декодируем JSON ответ
$data = json_decode($response, true);

// Проверяем наличие результатов геокодирования
if (!$data || !isset($data['response']['GeoObjectCollection']['featureMember'][0])) {
    echo json_encode(["error" => "No results found"]);
    exit;
}

// Извлекаем информацию о координатах и полном имени места
$geoObject = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
$addressText = $geoObject['metaDataProperty']['GeocoderMetaData']['text']; // Полный адрес
$structured = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components']; // Структурированный адрес
$coords = explode(" ", $geoObject['Point']['pos']); // Координаты (долгота и широта)
$lon = $coords[0]; // Долгота
$lat = $coords[1]; // Широта

// Формируем URL для поиска ближайшего метро по координатам
$metroUrl = "https://geocode-maps.yandex.ru/1.x/?apikey=$apiKey&geocode=$lon,$lat&kind=metro&lang=$lang&format=$format";
$metroResponse = @file_get_contents($metroUrl);

// Проверяем успешность запроса информации о метро
if ($metroResponse === false) {
    echo json_encode(["error" => "Ошибка подключения к API Яндекс.Карт"]);
    exit;
}

$metroData = json_decode($metroResponse, true);

// Функция для расчета расстояния между двумя точками по формуле Хаверсина
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // Радиус Земли в метрах
    
    // Переводим градусы в радианы
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    
    // Разница координат
    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;
    
    // Формула Хаверсина
    $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
    $c = 2 * asin(sqrt($a));
    
    return $earthRadius * $c; // Расстояние в метрах
}

// Инициализация переменных для информации о метро
$nearestMetro = "Не найдено";
$metroDistance = null;

// Проверяем наличие результатов поиска метро
if (isset($metroData['response']['GeoObjectCollection']['featureMember'][0])) {
    $metroObject = $metroData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
    $nearestMetro = $metroObject['name']; // Название станции метро
    $metroCoords = explode(" ", $metroObject['Point']['pos']);
    $metroLon = $metroCoords[0]; // Долгота метро
    $metroLat = $metroCoords[1]; // Широта метро
    
    // Рассчитываем расстояние до метро
    $metroDistance = haversineDistance($lat, $lon, $metroLat, $metroLon);
}

// Формируем и выводим итоговый JSON ответ
echo json_encode([
    "formatted_address" => $addressText, // Полный адрес
    "structured_address" => $structured, // Структурированный адрес (компоненты)
    "coordinates" => ["longitude" => $lon, "latitude" => $lat], // Координаты
    "nearest_metro" => $nearestMetro, // Ближайшее метро
    "distance_to_metro_meters" => ($nearestMetro != "Не найдено") ? (round($metroDistance, 2) .  ' метров от вас') : "-" // Расстояние до метро в метрах
]);