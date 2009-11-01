<?php
if(Context::showMap()) { 
    $width = isset($map_width) ? $map_width : 230;
    $height = isset($map_height) ? $map_height : 230; ?>
    <h2><?php __('Map'); ?></h2>
    <div id="widget_map" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px"></div>
<?php } ?>