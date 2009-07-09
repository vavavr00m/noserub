<?php
    $entry_filter = Context::entryFilter();
    
    $show_filter = array();
    foreach($service_types as $key => $value) {
        if(!in_array($key, $entry_filter)) {
            $show_filter[] = $html->link('(+)', '/entries/add_filter/' . $key) . ' ' . __($value, true);
        }
    }
?>
<div class="widget widget-entry-filter">
    <h2><?php __('Entry filter'); ?></h2>
    <?php if(!empty($entry_filter)) { ?>
        <ul>
            <?php foreach($entry_filter as $value) { ?>
                <li><?php echo $html->link('(x)', '/entries/remove_filter/' . $value) . ' ' . __($service_types[$value], true); ?></li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>
            <?php __('No filter selected'); ?>
        </p>
    <?php } ?>
    <ul>
        <?php foreach($show_filter as $link) {
            echo '<li>' . $link . '</li>';
        } ?>
    </ul>
</div>