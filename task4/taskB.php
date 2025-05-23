<?php
    $categories = [];
    
    while ($input = fgets(STDIN)) {
        $parts = explode(" ", trim($input)); 
        if (count($parts) < 4) break;
        
        list($id, $name, $left, $right) = $parts;
        
        // Добавляем категории в массив
        $categories[] = [
            'id' => $id,
            'name' => $name,
            'left' => $left,
            'right' => $right
        ];
    }
    
    function cmp($a, $b) {
        return $a['left'] <=> $b['left'];
    }
    // Сортируем массив по значению left key
    usort($categories, "cmp");
    
    // Стек для отслеживания уровней вложенности
    $levelStack = []; 
    
    foreach ($categories as $category) {
        // Убираем уровни, если находимся на выходе из узла
        while (!empty($levelStack) && end($levelStack)['right'] < $category['right']) {
            array_pop($levelStack);
        }
        
        // Уровень вложенности - это размер стека
        $level = count($levelStack);
        echo str_repeat("-", $level) . $category['name'] . "\n";
        
        // Добавляем текущий узел в стек
        $levelStack[] = $category;
    }
    
?>