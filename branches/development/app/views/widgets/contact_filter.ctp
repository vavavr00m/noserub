<?php
    $contact_filter = Context::contactFilter();
    
    $show_filter_tags = array();
    foreach($contact_tags['noserub_contact_type_ids'] as $tag) {
        if(!in_array($tag, $contact_filter)) {
            $show_filter_tags[] = $html->link('(+)', '/contacts/add_filter/' . $tag) . ' ' . $tag;
        }
    }
    foreach($contact_tags['contact_type_ids'] as $tag) {
        if(!in_array($tag, $contact_filter)) {
            $show_filter_tags[] = $html->link('(+)', '/contacts/add_filter/' . $tag) . ' ' . $tag;
        }
    }
?>
<div class="widget widget-contact-filter">
    <?php if(!empty($contact_tags)) { ?>
        <h2><?php __('Contact filter'); ?></h2>
        <?php if(!empty($contact_filter)) { ?>
            <ul>
                <?php foreach($contact_filter as $tag) { ?>
                    <li><?php echo $html->link('(x)', '/contacts/remove_filter/' . $tag) . ' ' . $tag; ?></li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>
                <?php __('No filter selected'); ?>
            </p>
        <?php } ?>
        <ul>
            <?php if(Context::isPage('activities')) {
                if($show_filter_tags && !in_array('me', $contact_filter)) { ?>
                    <li><?php echo $html->link('(+)', '/contacts/add_filter/me') . ' ' . __('me', true); ?></li>
                <?php }
            } ?>
            <?php foreach($show_filter_tags as $link) {
                echo '<li>' . $link . '</li>';
            } ?>
        </ul>
    <?php } ?>
</div>