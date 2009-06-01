<h2><?php __('Groups'); ?></h2>
<?php if($groups) { ?>
    <?php echo $this->element('groups/box'); ?>
<?php } else { ?>
    <p><?php
        __('This user currently is not subscribed to any group.');
    ?></p>
<?php } ?>