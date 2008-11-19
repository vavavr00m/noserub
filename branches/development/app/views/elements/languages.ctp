<?php

echo __('Also available in', true) . ': '; 

$i = 0;
$languages = Configure::read('Languages');
foreach($languages as $key => $value) {
    if($i > 0) {
        echo ' - ';
    }
    echo $html->link($value, '/pages/switch/language/' . $key . '/');
    $i++;
}