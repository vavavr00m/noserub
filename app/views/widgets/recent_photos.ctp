<?php
    $items = array();
    foreach($data as $item) {
        $raw_content = @unserialize(@base64_decode($item['Entry']['content']));
        if(!empty($raw_content['thumb'])) {
            $image = $html->image($raw_content['thumb'], array('width' => 75, 'height' => 75));
            $items[] = $html->link($image, '/entry/' . $item['Entry']['id'], array('escape' => false));
        } else {
            $items[] = $html->link($item['Entry']['content'], '/entry/' . $item['Entry']['id'], array('escape' => false));
        }
    }
?>
<div class="widget widget-photos">
    <?php if($items) { ?>
        <h2><?php __('Photos'); ?></h2>
        <ul class="photos">
            <?php foreach($items as $item) {
                echo '<li>' . $item . '</li>';
            } ?>
        </ul>
    <?php } ?>
</div>