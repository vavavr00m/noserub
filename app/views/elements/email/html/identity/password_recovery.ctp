<h1>
	<?php echo sprintf(__('You requested a new password for %s', true), 'http://' . $username); ?>
</h1>
<p>
	<a href="<?php echo Router::url('/pages/password/recovery/' . $recovery_hash . '/', true); ?>">
		<?php __('Please click here to set a new password'); ?>
	</a>
</p>
<p>
	<?php __('Thanks!'); ?>
</p>