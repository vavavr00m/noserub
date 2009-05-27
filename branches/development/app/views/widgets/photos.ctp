<h2><?php __('Photos'); ?></h2>
<ul class="photos">
    <?php foreach($data as $item) {
        echo '<li>';
        echo $html->link($item['Entry']['content'], '/entry/' . $item['Entry']['id'], array(), false, false);
        echo '</li>';
    } ?>
</ul>
<p class="more">
	<a href="#"><?php __('show more'); ?></a>
</p>