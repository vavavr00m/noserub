<?php
    $items = array();
    foreach($data as $item) {
        $raw_content = @unserialize(@base64_decode($item['Entry']['content']));
        if(!empty($raw_content['thumb'])) {
            $image = $html->image($raw_content['thumb'], array('width' => 75, 'height' => 75));
            $items[] = $html->link($image, '/entry/' . $item['Entry']['id'], array('escape' => false));
        }
    }
?>
<div class="widget widget-videos">
    <?php if($items) { ?>
        <h2><?php __('Videos'); ?></h2>
        <ul class="photos">
            <?php foreach($items as $item) {
                echo '<li>' . $item . '</li>';
            } ?>
        </ul>
    <?php } ?>
</div>