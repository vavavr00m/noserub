<h2>Synced usernames</h2>
<?php if(!is_array($data)) {
    echo $data;
} else { ?>
    <ul>
        <?php foreach($data as $item) { ?>
            <li><?php echo $item; ?></li>
        <?php } ?>
    </ul>
<?php } ?>