<form id="OpenIDLoginForm" method="post" action="<?php echo $this->here; ?>">
	<fieldset>
	<h2><?php __('Sign in with OpenID'); ?></h2>
	<label><img src="<?php echo Router::url('/images/openid_small.gif'); ?>" alt="OpenID logo" /><?php __('Your OpenID'); ?></label>
	<?php echo $form->input('Identity.openid', array('label' => false, 
						    'error' => array('invalid_openid'         => __('Invalid OpenID', true),
											 'verification_cancelled' => __('Verification cancelled', true),
											 'openid_failure'         => sprintf(__('OpenID authentication failed: %s', true), @$errorMessage)))); ?>
		<div class="note"><?php __('e.g. http://user123.myopenid.com/'); ?></div>
		<div class="submit">
			<input type="submit" value="<?php __('Login'); ?>"/>
		</div>
	</fieldset>
</form>
