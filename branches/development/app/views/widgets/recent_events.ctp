<?php if($data) { ?>
    <div class="widget widget-events halfwidth">
        <h2><?php __('Events'); ?></h2>
            <?php foreach($data as $item) {
                echo '<li>';
                echo $html->link(strip_tags($item['Entry']['title']), '/entry/' . $item['Entry']['id']);
                echo '</li>';
            } ?>
        </ul>
    </div>
<?php } ?>