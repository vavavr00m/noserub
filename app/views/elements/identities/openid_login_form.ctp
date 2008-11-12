<form id="OpenIDLoginForm" method="post" action="<?php echo $this->here; ?>">
	<fieldset>
	<legend><?php __('Sign in with OpenID'); ?></legend>
	<label><img src="<?php echo Router::url('/images/openid_small.gif'); ?>" alt="OpenID logo" /><?php __('Your OpenID'); ?></label>
	<?php echo $form->input('Identity.openid', array('label' => false, 
						    'error' => array('invalid_openid'         => __('Invalid OpenID', true),
											 'verification_cancelled' => __('Verification cancelled', true),
											 'openid_failure'         => sprintf(__('OpenID authentication failed: %s', true), @$errorMessage)))); ?>
											 <p><?php __('e.g. http://user123.myopenid.com/'); ?></p>
	<input class="submitbutton" type="submit" value="<?php __('Login'); ?>"/>
	</fieldset>
</form>