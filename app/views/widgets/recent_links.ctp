<?php if($data) { ?>
    <div class="widget widget-links halfwidth">
        <h2><?php __('Links'); ?></h2>
            <?php foreach($data as $item) {
                echo '<li>';
                echo $html->link(strip_tags($item['Entry']['title']), '/entry/' . $item['Entry']['id']);
                echo '</li>';
            } ?>
        </ul>
    </div>
<?php } ?>