<h2><?php __('Polled comments'); ?></h2>
<?php if(!is_array($comments)) {
    echo $comments;
} else { ?>
    <ul>
        <?php foreach($comments as $item) { ?>
            <li><?php echo $item; ?></li>
        <?php } ?>
    </ul>
<?php } ?>
<h2><?php __('Polled favorites'); ?></h2>
<?php if(!is_array($favorites)) {
    echo $favorites;
} else { ?>
    <ul>
        <?php foreach($favorites as $item) { ?>
            <li><?php echo $item; ?></li>
        <?php } ?>
    </ul>
<?php } ?>