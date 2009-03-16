<?php __('Groups'); ?>
<?php if($groups) { ?>
    <ul>
        <?php foreach($groups as $group) { ?>
            <li>
                <?php echo $html->link($group['name'], '/groups/' .$group['slug'] . '/'); ?>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p><?php
        __('This user currently is not subscribed to any group.');
    ?></p>
<?php } ?>