<form method="post" action="/pages/oauth/authorize">
	<input type="hidden" name="oauth_token" value="<?php echo $oauth_token; ?>" />
	<input type="hidden" name="oauth_callback" value="<?php echo $oauth_callback; ?>" />
	<input type="submit" />
</form>