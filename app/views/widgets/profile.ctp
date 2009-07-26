<?php
$base_url = '/' . $data['local_username'] . '/';
$name = $data['name'];
?>
<div class="widget widget-profile">
    <div class="vcard">
    	<?php echo $html->image($noserub->fnProfilePhotoUrl(), array('class' => 'photo', 'alt' => $name, 'width' => 130, 'height' => 130)); ?>
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
        <?php echo $noserub->link('/contact/manage/'); ?>
    	<a class="button send-message" href="#">
			<span></span>
    		Send a message
    	</a>
    	<ul>
    		<li><a href="#">Download vCard</a></li>
    		<li><a href="#">RSS-Feed</a></li>
    	</ul>
    </div>
    <ul>
        <li<?php echo Context::isPage('profile.home') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Profile', true), $base_url); ?></li>
        <li<?php echo Context::isPage('profile.activities') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Activities', true), $base_url . 'activities/'); ?></li>
        <li<?php echo Context::isPage('profile.contacts') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Contacts', true), $base_url . 'contacts/'); ?></li>
        <li<?php echo Context::isPage('profile.groups') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Groups', true), $base_url . 'groups/'); ?></li>
        <li<?php echo Context::isPage('profile.locations') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Locations', true), $base_url . 'locations/'); ?></li>
        <li<?php echo Context::isPage('profile.events') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Events', true), $base_url . 'events/'); ?></li>
        <li<?php echo Context::isPage('profile.networks') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Networks', true), $base_url . 'networks/'); ?></li>
    </ul>
</div>
