<p>
	<?php __('Allow access?'); ?> <?php // TODO a better text needed ;-) ?>
</p>
<form method="post" action="/pages/omb/authorize_form">
	<input type="submit" value="<?php __('allow'); ?>" name="allow" />
	<input type="submit" value="<?php __('deny'); ?>" name="deny" />
</form>