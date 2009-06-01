<?php
    // actually, this is the same as elements/groups/box.ctp, but
    // the markup will be different with the "real" theme/design. 
?>
<ul>
    <?php foreach($groups as $group) { ?>
        <?php
            if(isset($group['Group'])) {
               $group = $group['Group']; 
            }
        ?>
        <li>
            <?php echo $html->link($group['name'], '/groups/view/' .$group['slug'] . '/'); ?>
        </li>
    <?php } ?>
</ul>