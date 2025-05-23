<?php
    //получаем html
    $oldHtml = trim(fgets(STDIN));
    // паттерн для замены
    $pattern = '%http://asozd\.duma\.gov\.ru/main\.nsf/\(Spravka\)\?OpenAgent(&amp;|&)RN=([0-9-]+)(&amp;|&)\d+%';
    // на что заменяем
    $newReplace = 'http://sozd.parlament.gov.ru/bill/$2';
    // заменяем в html
    $newHtml = preg_replace($pattern, $newReplace, $oldHtml);
    echo "\n\n\n\n";
    echo $newHtml;
?>