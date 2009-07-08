<?php 
    $content = $data['Entry']['content'];
    $raw_content = @unserialize(@base64_decode($content));

    if(!empty($raw_content['photo'])) {
        echo $html->image($raw_content['photo']); 
    } else {
        echo $content;
    }
?>