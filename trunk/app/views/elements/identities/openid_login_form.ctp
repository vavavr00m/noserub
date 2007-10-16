<form id="OpenIDLoginForm" method="post" action="<?php echo $this->here; ?>">
	<img src="/images/openid_small.gif" alt="OpenID logo" /> OpenID
	<?php echo $form->input('Identity.openid', array('label' => false, 
						    'error' => array('invalid_openid' => 'Invalid OpenID',
											 'verification_cancelled' => 'Verification cancelled',
											 'openid_failure' => 'OpenID authentication failed: '.@$errorMessage))); ?>
	<input class="submitbutton" type="submit" value="Login"/>
</form>