<form id="IdentityRegisterForm" method="post" action="<?php echo $this->here; ?>">
	<?php echo $form->input('Identity.openid', array('label' => 'OpenID', 
						    'error' => array('invalid_openid' => 'Invalid OpenID',
											 'verification_cancelled' => 'Verification cancelled',
											 'openid_failure' => 'OpenID authentication failed: '.@$errorMessage))); ?>
	<input class="submitbutton" type="submit" value="Register"/>
</form>
