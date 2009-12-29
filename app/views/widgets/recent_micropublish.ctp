<?php if($data) { ?>
    <div class="widget widget-micropublish halfwidth">
        <h2><?php __('Micropublish'); ?></h2>
            <?php foreach($data as $item) {
                echo '<li>';
                echo $html->link(htmlspecialchars_decode(strip_tags($item['Entry']['title']), ENT_QUOTES), '/entry/' . $item['Entry']['id']);
                echo '</li>';
            } ?>
        </ul>
    </div>
<?php } ?>