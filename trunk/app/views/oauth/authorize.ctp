<p>
	<?php echo sprintf(__('Application <strong>%s</strong> asked for permission to access your data', true), $applicationName); ?>
</p>
<form method="post" action="/pages/oauth/authorize">
	<input type="submit" value="<?php __('allow'); ?>" name="allow" />
	<input type="submit" value="<?php __('deny'); ?>" name="deny" />
</form>