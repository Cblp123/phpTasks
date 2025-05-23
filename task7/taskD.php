<?php

function buildTree($items) {
    $tree = [];
    $timestamps = [];

    // Сначала создаем массив всех узлов
    foreach ($items as $item) {
        list($id, $url, $parent_id, $time) = explode(';', $item);
        $tree[$id] = [
            'id' => (int)$id,
            'url' => $url,
            'parent_id' => (int)$parent_id,
            'time' => (int)$time,
            'children' => []
        ];
        $timestamps[$id] = (int)$time;
    }

    // Строим дерево, добавляя узлы к их родителям
    foreach ($tree as $id => &$node) {
        if ($node['parent_id'] !== 0 && isset($tree[$node['parent_id']])) {
            $tree[$node['parent_id']]['children'][] = &$node;
        }
    }

    return [$tree, $timestamps];
}

function updateTimestamps(&$timestamps, $tree, $node_id) {
    if (!isset($tree[$node_id])) {
        return $timestamps[$node_id];
    }

    $max_time = $tree[$node_id]['time'];
    
    // Проверяем времена всех дочерних узлов
    foreach ($tree[$node_id]['children'] as $child) {
        $child_time = updateTimestamps($timestamps, $tree, $child['id']);
        $max_time = max($max_time, $child_time);
    }

    $timestamps[$node_id] = $max_time;
    return $max_time;
}

function formatDateTime($timestamp) {
    return date('Y-m-d\TH:i:sP', $timestamp);
}

function generateSitemap($items) {
    // Строим дерево и получаем массив временных меток
    list($tree, $timestamps) = buildTree($items);

    // Обновляем временные метки с учетом дочерних элементов
    foreach ($tree as $node) {
        if ($node['parent_id'] === 0) {
            updateTimestamps($timestamps, $tree, $node['id']);
        }
    }

    // Сортируем узлы по ID
    ksort($tree);

    // Формируем XML
    $output = '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($tree as $node) {
        $output .= '<url>';
        $output .= '<loc>' . htmlspecialchars($node['url']) . '</loc>';
        $output .= '<lastmod>' . formatDateTime($timestamps[$node['id']]) . '</lastmod>';
        $output .= '</url>';
    }

    $output .= '</urlset>';
    return $output;
}

// Читаем входные данные
$input = trim(file_get_contents('dataD.txt'));
$items = explode("\n", $input);

// Генерируем и выводим sitemap
echo generateSitemap($items);
