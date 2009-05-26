<?php
$base_url = '/' . $data['local_username'] . '/';
$name = $data['name'];
?>
<?php echo $html->image($noserub->fnProfilePhotoUrl(), array('class' => 'userimage', 'alt' => $name, 'width' => 130, 'height' => 130)); ?>
<div class="vcard">
    <h1 class="fn"><?php echo $name; ?></h1>
    <?php echo $html->link($data['username'], 'http://' . $data['username'], array('class' => 'url')); ?>
    <p class="role">
        Art Director &amp; Designer
    </p>
    <p class="adr">
        <span class="street-address">Wesselinger Straße 22-30</span>,
		<span class="postal-code">50999</span>
	    <span class="locality">Köln</span>
    </p>
</div>
<div class="buttons">
    <?php echo $noserub->link('/add/as/contact/'); ?>
	<a class="button-send-message" href="#">
		Send a message
	</a>
	<ul>
		<li><a href="#">Download vCard</a></li>
		<li><a href="#">RSS-Feed</a></li>
	</ul>
</div>
<ul>
    <li class="active"><?php echo $html->link(__('Profile', true), $base_url); ?></li>
    <li><?php echo $html->link(__('Lifestream', true), $base_url . 'activities/'); ?></li>
    <li><?php echo $html->link(__('Contacts', true), $base_url . 'contacts/'); ?></li>
    <li><?php echo $html->link(__('Groups', true), $base_url . 'groups/'); ?></li>
    <li><?php echo $html->link(__('Networks', true), $base_url . 'networks/'); ?></li>
</ul>