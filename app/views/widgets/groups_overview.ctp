<?php __('Groups'); ?>
<?php if($groups) { ?>
    <ul>
        <?php foreach($groups as $group) { ?>
            <li>
                <?php echo $html->link($group['Group']['name'], '/groups/' .$group['Group']['slug'] . '/'); ?>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p><?php
        __('There are currently no groups available.');
    ?></p>
<?php } ?>
<?php echo $noserub->link('/groups/add/'); ?>