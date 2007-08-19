<?php if(empty($data)) { ?>
    <p>
        No items there yet.
    </p>
<?php } else if($filter == 'media') { ?>
    <?php foreach($data as $item) { ?>
        <div style="float:left;padding:10px;">
            <?php echo $item['content']; ?><br />
            <?php echo $html->link($item['username'], $item['link']); ?>
        </div>
    <?php } ?>
<? } else { ?>
    <ul>
        <?php foreach($data as $item) { ?>
            <li><?php 
                    echo $item['datetime'] . ': ' . $item['username'] . ' => ';
                    echo $html->link('[+]', $item['link']);
                echo $item['content']; 
            ?></li>
        <?php } ?>
    </ul>
<?php } ?>