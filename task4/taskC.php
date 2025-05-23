<?php

$dict = array();
$sum = 0;
// Считываем входные данные и подсчитываем веса баннеров
while ($input = fgets(STDIN)) {
    $input = trim($input); 
    if (strlen($input) < 3) break;
    $string = explode(" ", $input); 
    // Считываем идентификатор баннера и его вес
    $bannerId = $string[0];
    $bannerWeight = $string[1];

    // Добавляем вес баннера в словарь и увеличиваем сумму весов
    $sum += $bannerWeight;
    
    $dict[$bannerId] = $bannerWeight;
}

// Выводим результаты

foreach ($dict as $bannerId => $bannerWeight) {
    echo $bannerId, " ", round($bannerWeight / $sum, 6), "\n";
}

?>