<?php
    $base_url = '/' . Configure::read('context.logged_in_identity.local_username') . '/';
?>
<ul>	
    <li><?php echo $html->link(__('Profile', true), $base_url . 'settings/profile/') ?></li>
    <li><?php echo $html->link(__('Accounts', true), $base_url . 'settings/accounts/') ?></li>
    <li><?php echo $html->link(__('Locations', true), $base_url . 'settings/locations/') ?></li>
	<li><?php echo $html->link(__('Display', true), $base_url . 'settings/display/') ?></li>
	<li><?php echo $html->link(__('Privacy', true), $base_url . 'settings/privacy/') ?></li>
	<li><?php echo $html->link(__('Feeds', true), $base_url . 'settings/feeds/') ?></li>
	<li><?php echo $html->link(__('OpenID', true), $base_url . 'settings/openid/') ?></li>
	<li><?php echo $html->link(__('OAuth', true), $base_url . 'settings/oauth/') ?></li>
	<li><?php echo $html->link(__('Password & API', true), $base_url . 'settings/password/') ?></li>
	<li><?php echo $html->link(__('Manage', true), $base_url . 'settings/account/') ?></li>
</ul>