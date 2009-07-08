<?php 
    $content = $data['Entry']['content'];
    $raw_content = @unserialize(@base64_decode($content));

    if(!empty($raw_content['embedd'])) {
        echo $raw_content['embedd']; 
    } else {
        echo $content;
    }
?>