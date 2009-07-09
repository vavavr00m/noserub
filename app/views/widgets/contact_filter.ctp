<?php $contact_filter = Context::contactFilter(); ?>
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
                if(!in_array('me', $contact_filter)) { ?>
                    <li><?php echo $html->link('(+)', '/contacts/add_filter/me') . ' ' . __('me', true); ?></li>
                <?php }
            } ?>
            <?php foreach($contact_tags['noserub_contact_type_ids'] as $tag) { ?>
                <?php if(!in_array($tag, $contact_filter)) { ?>
                    <li><?php echo $html->link('(+)', '/contacts/add_filter/' . $tag) . ' ' . $tag; ?></li>
                <?php } ?>
            <?php } ?>
            <?php foreach($contact_tags['contact_type_ids'] as $tag) { ?>
                <?php if(!in_array($tag, $contact_filter)) { ?>
                    <li><?php echo $html->link('(+)', '/contacts/add_filter/' . $tag) . ' ' . $tag; ?></li>
                <?php } ?>
            <?php } ?>
        </ul>
    <?php } ?>
</div>