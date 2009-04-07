<ul>
    <?php foreach($groups as $group) { ?>
        <li>
            <?php echo $html->link($group['Group']['name'], '/groups/view/' .$group['Group']['slug'] . '/'); ?>
        </li>
    <?php } ?>
</ul>