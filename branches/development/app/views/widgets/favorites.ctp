<h2><?php __('Entries I favorited'); ?></h2>
<?php if($items) { ?>
            <?php echo $this->element('identities/items', array('data' => $items)); ?>
<?php } else { ?>
    <p>
        <?php __('There are no favorites yet.'); ?>
    </p>
<?php } ?>