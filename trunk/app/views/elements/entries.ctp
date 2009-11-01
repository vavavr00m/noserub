<?php

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 days'));
$days = 0;
foreach($data as $date => $cluster) {
    $days++;
    if($days === 14) {
        break;
    }

    if($date == $today) { 
        echo '<h3>' . __('Today', true) . '</h3>'; 
    } else if($date == $yesterday) {
        echo '<h3>' . __('Yesterday', true) . '</h3>';
    } else {
        echo '<h3>' . date('F jS, Y', strtotime($date)) . '</h3>';
    }
    echo '<ul class="lifestream">';

    foreach($cluster as $item) {
        echo $this->renderElement('entries/row_view', array('item' => $item, 'with_date' => ($date != $today)));    
    }                    
    echo '</ul>';
}