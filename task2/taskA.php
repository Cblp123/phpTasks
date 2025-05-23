<?php 
    // кол-во ставок
    $n = fgets(STDIN);
    $bets = [];
    // считываем ставки и добавляем их в массив $bets
    for ($i = 0; $i < $n; $i++) {
        $bet = explode(" ", trim(fgets(STDIN)));
        $bets[$bet[0]][$bet[2]] = $bet[1];
    }
    $balance = 0;
    // кол-во игр
    $m = fgets(STDIN);
    // коэффициенты выигрыша
    $indexKoff = ['L' => 1, 'R' => 2, 'D' => 3];
    // считываем игры и вычисляем баланс
    for ($i = 0; $i < $m; $i++) {
        $play = explode(" ", trim(fgets(STDIN)));
        $numberOfPlay = $play[0];
        $result = $play[4];
        $koff = $play[$indexKoff[$result]];
        // проверяем, есть ли ставка на данную игру
        if (array_key_exists($numberOfPlay, $bets)){
            if (array_key_exists($result, $bets[$numberOfPlay])) {
                $balance += $bets[$numberOfPlay][$result] * ($koff - 1);
            }
            else {
                foreach ($bets[$numberOfPlay] as $key => $value) {
                    $balance -= $value;
                }
            }
        }
    }
    // выводим текущий баланс
    echo($balance);

?>