<div id="bd-main">
	<div id="bd-main-hd">
		<h2><?php __('Account Settings'); ?></h2>
		<p>
			<?php __('Here you can add all your own social/online activities and import friends in your network.'); ?>
		</p>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->formAccounts(); ?>
		
		<?php echo $noserub->accountSettingsTwitter(); ?> 
	</div>
</div>
