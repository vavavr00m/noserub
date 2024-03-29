<?php
$base_url = '/' . $data['local_username'] . '/';
$name = $data['name'];

if(isset($data['openid'])) {
	# We delegate to the OpenID identity instead of the OpenID as the OpenID itself may be 
	# delegated and because OpenID delegation chaining is not possible our delegation 
	# wouldn't work.
	$openid->delegate($data['openid_identity'], false);
	$openid->serverLink($data['openid_server_url'], false);
} else {
	$openid->xrdsLocation($base_url . '/xrds', false);
	$openid->serverLink('/auth', false);
}
?>
<div class="widget widget-profile">
    <div class="vcard">
    	<?php echo $html->image($noserub->fnProfilePhotoUrl(), array('class' => 'photo', 'alt' => $name, 'width' => 130, 'height' => 130)); ?>
        <h1 class="fn"><?php echo $name; ?></h1>
        <?php echo $html->link($data['username'], 'http://' . $data['username'], array('class' => 'url')); ?>
        <?php if($data['title']) { ?>
            <p class="role"><?php echo $data['title']; ?></p>
        <?php } ?>
        <?php if($data['address_shown']) { ?>
            <p class="adr">
                <span class="address"><?php echo $data['address_shown']; ?></span>
            </p>
        <?php } ?>
    </div>
    <div class="buttons">
        <?php echo $noserub->link('/contact/manage/'); ?>
        <?php echo $noserub->link('/message/send/'); ?>
    	<ul>
    		<li class="vcard">
    		    <span class="icon"></span>
    		    <?php echo $html->link(__('Download vCard', true), $base_url . 'vcard'); ?>
    		</li>
    		<li class="feed">
    		    <span class="icon"></span>
    		    <?php echo $html->link(__('RSS-Feed', true), $base_url . 'feed'); ?>
    		</li>
    	</ul>
    </div>
    <ul>
        <li<?php echo Context::isPage('profile.home') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Profile', true), $base_url); ?></li>
        <li<?php echo Context::isPage('profile.activities') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Activities', true), $base_url . 'activities/'); ?></li>
        <li<?php echo Context::isPage('profile.contacts') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Contacts', true), $base_url . 'contacts/'); ?></li>
        <li<?php echo Context::isPage('profile.groups') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Groups', true), $base_url . 'groups/'); ?></li>
<?php 
/*        
        <li<?php echo Context::isPage('profile.locations') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Locations', true), $base_url . 'locations/'); ?></li>
        <li<?php echo Context::isPage('profile.events') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Events', true), $base_url . 'events/'); ?></li>
        <li<?php echo Context::isPage('profile.networks') ? ' class="active"' : ''; ?>><?php echo $html->link(__('Networks', true), $base_url . 'networks/'); ?></li>
*/
?>
    </ul>
</div>
