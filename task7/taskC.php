<?php
// Функция для форматирования значения для SQL-запроса
function formatValue($value) {
    if (is_string($value)) {
        return "'" . $value . "'";
    } elseif (is_null($value)) {
        return "null";
    } elseif (is_bool($value)) {
        return $value ? "true" : "false";
    }
    return $value;
}

// Функция для разбора оператора из ключа условия
function parseOperation($key) {
    $operations = [
        '<=' => '<=',
        '>=' => '>=',
        '<' => '<',
        '>' => '>',
        '=' => '=',
        '!' => '!='
    ];

    foreach ($operations as $op => $sqlOp) {
        if (strpos($key, $op) === 0) {
            return [
                'field' => substr($key, strlen($op)),
                'operator' => $sqlOp
            ];
        }
    }

    return [
        'field' => $key,
        'operator' => null
    ];
}

// Функция для построения одного условия WHERE
function buildCondition($key, $value) {
    // Обработка AND/OR условий
    if (strpos($key, 'and_') === 0) {
        return "(" . buildWhere($value) . ")";
    }
    if (strpos($key, 'or_') === 0) {
        $subParts = [];
        foreach ($value as $subKey => $subValue) {
            $subParts[] = buildCondition($subKey, $subValue);
        }
        return "(" . implode(" or ", $subParts) . ")";
    }

    $parsed = parseOperation($key);
    $field = $parsed['field'];
    $operator = $parsed['operator'];

    // Обработка специальных случаев
    if (is_null($value)) {
        return $field . ($operator === '!=' ? " is not null" : " is null");
    }
    if (is_bool($value)) {
        return $field . ($operator === '!=' ? " is not " : " is ") . ($value ? "true" : "false");
    }

    // Обработка обычных операторов
    if ($operator) {
        return $field . " " . $operator . " " . formatValue($value);
    }

    // Обработка случая без оператора
    if (is_string($value)) {
        return $field . " like " . formatValue($value);
    }
    return $field . " = " . formatValue($value);
}

// Функция для построения части WHERE из массива условий
function buildWhere($conditions) {
    if (empty($conditions)) {
        return "";
    }

    $parts = [];
    foreach ($conditions as $key => $value) {
        $parts[] = buildCondition($key, $value);
    }

    return implode(" and ", $parts);
}

// Функция для построения полного SQL-запроса
function buildQuery($data) {
    // Формируем SELECT
    $select = empty($data['select']) ? "select *" : "select " . implode(", ", $data['select']);
    
    // Формируем FROM
    $from = "from " . $data['from'];
    
    // Формируем WHERE
    $where = "";
    if (!empty($data['where'])) {
        $where = "where " . buildWhere($data['where']);
    }
    

    $order = "";
    if (!empty($data['order'])) {
        $field = key($data['order']);
        $direction = $data['order'][$field];
        $order = "order by " . $field . " " . strtolower($direction);
    }
    
    // Формируем LIMIT
    $limit = "";
    if (!empty($data['limit'])) {
        $limit = "limit " . $data['limit'];
    }
    
    // Собираем все части запроса
    $parts = [$select, $from];
    if ($where) $parts[] = $where;
    if ($order) $parts[] = $order;
    if ($limit) $parts[] = $limit;
    
    return implode("\n", $parts) . ";";
}

// Читаем входные данные
$json = file_get_contents('dataC.txt');
$data = json_decode($json, true);

// Генерируем SQL запрос
echo buildQuery($data) . "\n";
