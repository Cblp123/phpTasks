<?php
list($n, $m) = explode(" ", trim(fgets(STDIN)));
$matrix = array();
for ($i = 0; $i < $n; $i++) {
    for ($j = 0; $j < $n; $j++) {
        $matrix[$i][$j] = 0;
    }
}

for ($i = 0; $i < $m; $i++) {
    list($a, $b, $w) = explode(" ", trim(fgets(STDIN)));
    $matrix[$a][$b] = $w;
    $matrix[$b][$a] = $w;
}

$k = trim(fgets(STDIN));

for ($i = 0; $i < $k; $i++) {
    list($c, $d, $r) = explode(" ", trim(fgets(STDIN)));
    if ($r == '?') {
        $time = solve($matrix, $c, $d);
        echo $time. "\n";
    } elseif ($r == -1) {
        $matrix[$c][$d] = 0;
        $matrix[$d][$c] = 0;
    } else {
        $matrix[$c][$d] = $r;
        $matrix[$d][$c] = $r;
    }
}

function solve($matrix, $x, $y) {
    $n = count($matrix);
    $dist = array_fill(0, $n, INF);
    $visited = array_fill(0, $n, false);
    $dist[$x] = 0;

    for ($i = 0; $i < $n - 1; $i++) {
        $u = minDistance($dist, $visited, $n);
        $visited[$u] = true;

        for ($v = 0; $v < $n; $v++) {
            if (!$visited[$v] && $matrix[$u][$v] != 0 && $dist[$u] != INF && $dist[$u] + $matrix[$u][$v] < $dist[$v]) {
                $dist[$v] = $dist[$u] + $matrix[$u][$v];
            }
        }
    }

    return $dist[$y] == INF ? -1 : $dist[$y];
}

function minDistance($dist, $visited, $n) {
    $min = INF;
    $minIndex = -1;

    for ($v = 0; $v < $n; $v++) {
        if (!$visited[$v] && $dist[$v] <= $min) {
            $min = $dist[$v];
            $minIndex = $v;
        }
    }

    return $minIndex;
}
?>