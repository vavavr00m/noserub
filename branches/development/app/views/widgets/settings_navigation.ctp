<div class="widget widget-settings-navigation">
    <ul>	
        <li><?php echo $html->link(__('Profile', true), '/settings/profile/') ?></li>
    	<li><?php echo $html->link(__('Display', true), '/settings/display/') ?></li>
    	<li><?php echo $html->link(__('Privacy', true), '/settings/privacy/') ?></li>
    	<?php if (Context::isTwitterFeatureEnabled()): ?>
    		<li><?php echo $html->link(__('Twitter', true), '/settings/twitter/') ?></li>
    	<?php endif; ?>
    	<li><?php echo $html->link(__('OpenID', true), '/settings/openid/') ?></li>
    	<li><?php echo $html->link(__('OAuth', true), '/settings/oauth/') ?></li>
    	<li><?php echo $html->link(__('Password', true), '/settings/password/') ?></li>
    	<li><?php echo $html->link(__('Manage', true), '/settings/account/') ?></li>
    	<li><?php echo $html->link(__('API', true), '/settings/api/') ?></li>
    </ul>
</div>