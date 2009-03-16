<?php if($groups) { ?>
    <?php __('Popular Groups'); ?>
    <ul>
        <?php foreach($groups as $group) { ?>
            <li>
                <?php echo $html->link($group['Group']['name'], '/groups/' .$group['Group']['slug'] . '/'); ?>
            </li>
        <?php } ?>
    </ul>
<?php } ?>