<?php if(!empty($contact_tags)) { ?>
    <ul>
        <?php foreach($contact_tags['noserub_contact_type_ids'] as $tag) { ?>
            <li><?php echo $html->link($tag, '/' . Configure::read('context.logged_in_identity.local_username') . '/network/rel:' . $tag); ?></li>
        <?php } ?>
        <?php foreach($contact_tags['contact_type_ids'] as $tag) { ?>
            <li><?php echo $html->link($tag, '/' . Configure::read('context.logged_in_identity.local_username') . '/network/rel:' . $tag); ?></li>
        <?php } ?>
    </ul>
<?php } ?>