<?php if(isset($data['Comment']) && count($data['Comment']) > 0 ) {
    echo '<br />';
    foreach($data['Comment'] as $idx => $item) {
        if($idx > 0){
            echo '<br />';
        }
        echo '»' . nl2br($item['content']) . '«';
        echo ' - <a href="http://' . $item['Identity']['username'] .'" title="' . $item['published_on'] . '">' . $item['Identity']['local_username'] . '</a>';
    }
} ?>