<?php __('Groups'); ?>
<?php if($groups) { ?>
    <?php echo $this->element('groups/list'); ?>
<?php } else { ?>
    <p><?php
        __('There are currently no groups available.');
    ?></p>
<?php } ?>
<?php echo $noserub->link('/groups/add/'); ?>