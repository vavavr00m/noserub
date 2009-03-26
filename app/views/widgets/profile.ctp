<?php
$base_url = '/' . Configure::read('context.identity.local_username') . '/';
$name = Configure::read('context.identity.name');
?>
<div class="vcard">
    <?php echo $html->image($noserub->fnProfilePhotoUrl(), array('class' => 'photo', 'alt' => $name)); ?>
    <?php echo $html->tag('span', $name, array('class' => 'fn')); ?>
    <?php echo $html->link(Configure::read('context.identity.username'), 'http://' . Configure::read('context.identity.username')); ?>
</div>
<?php echo $noserub->link('/add/as/contact/'); ?>
<div class="navi">
    <ul>
        <li><?php echo $html->link(__('Profile', true), $base_url); ?></li>
        <li><?php echo $html->link(__('Lifestream', true), $base_url . 'activities/'); ?></li>
        <li><?php echo $html->link(__('Contacts', true), $base_url . 'contacts/'); ?></li>
        <li><?php echo $html->link(__('Groups', true), $base_url . 'groups/'); ?></li>
        <li><?php echo $html->link(__('Networks', true), $base_url . 'networks/'); ?></li>
    </ul>
</div>