<form id="OpenIDLoginForm" method="post" action="<?php echo $this->here; ?>">
	<fieldset>
	<legend>Sign in with OpenID</legend>
	<label><img src="<?php echo Router::url('/images/openid_small.gif'); ?>" alt="OpenID logo" /> Your OpenID</label>
	<?php echo $form->input('Identity.openid', array('label' => false, 
						    'error' => array('invalid_openid' => 'Invalid OpenID',
											 'verification_cancelled' => 'Verification cancelled',
											 'openid_failure' => 'OpenID authentication failed: '.@$errorMessage))); ?>
											 <p>e.g. http://user123.myopenid.com/</p>
	<input class="submitbutton" type="submit" value="Login"/>
	</fieldset>
</form>