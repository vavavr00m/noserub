<ul class="block-groups">
    <?php foreach($groups as $group) { ?>
        <?php if(isset($group['Group'])) {
            $group = $group['Group'];
        } ?>
        <li>
            <?php echo $html->link($group['name'], '/groups/view/' .$group['slug'] . '/'); ?>
        </li>
    <?php } ?>
</ul>