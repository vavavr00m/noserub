<?php if($data) { ?>
    <div class="widget widget-texts halfwidth">
        <h2><?php __('Texts'); ?></h2>
            <?php foreach($data as $item) {
                echo '<li>';
                echo $html->link(strip_tags($item['Entry']['title']), '/entry/' . $item['Entry']['id']);
                echo '</li>';
            } ?>
        </ul>
    </div>
<?php } ?>