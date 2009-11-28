<div id="bd-main">
	<div id="bd-main-hd">
		<h1><?php __('Login'); ?></h1>
	</div>
	<div id="bd-main-bd">
		<?php // TODO: convert this error message to a flash message ?>
		<?php if(isset($form_error) && !empty($form_error)) { 
			echo '<p>'. $form_error . '</p>';
		} ?>
		<?php if (!$session->check('Noserub.lastOpenIDRequest')): ?>
			<?php echo $form->create('Identity', array('url' => '/pages/login', 'id' => 'IdentityPagesLoginOpenIDForm')); ?>
			<?php echo $form->input('Identity.openid', array('label' => sprintf(__('Your %s OpenID', true), '<img src="' . Router::url('/images/openid_small.gif') . '" alt="OpenID logo" /> '), 
										'error' => array('invalid_openid'         => __('Invalid OpenID', true),
														 'verification_cancelled' => __('Verification cancelled', true),
														 'openid_failure'         => sprintf(__('OpenID authentication failed: %s',true), @$errorMessage)))); ?>
			<br /><br />
			<?php echo $form->checkbox('Identity.remember', array('id' => 'IdentityRememberOpenID')); ?>&nbsp;<?php __('Remember me'); ?>
			<br /><br />
			<?php echo $form->end(__('Login', true)); ?>
		<?php endif; ?>

		<?php echo $form->create('Identity', array('url' => '/pages/login', 'id' => 'IdentityPagesLoginForm')); ?>
		<?php echo $form->input('Identity.username'); ?>
		<?php echo $form->input('Identity.password', array('type' => 'password')); ?>
		<br />
		<?php echo $form->checkbox('Identity.remember'); ?>&nbsp;<?php __('Remember me'); ?>
		<br /><br />
		<?php echo $form->end(__('Login', true)); ?>
		
		<p class="clear">
		<?php echo $html->link(__('Forgot your password?', true), '/pages/password/recovery/'); ?>
		<p>
	</div>
</div>
