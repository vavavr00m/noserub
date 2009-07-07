<ul>
	<li>
		<?php echo $form->create(array('id' => 'formLogin', 'url' => '/pages/login/')); ?>
			<fieldset>
				<?php echo $form->input('Identity.username'); ?>
				<?php echo $form->input('Identity.password', array('type' => 'password')); ?>
				<br />
				<?php echo $form->checkbox('Identity.remember'); ?>&nbsp;<?php __('Remember me'); ?>
				<br />
				<input type="submit" value="<?php __('Login'); ?>"/>
			</fieldset>
		<?php echo $form->end(); ?>
	</li>
	<?php if(!$session->check('Noserub.lastOpenIDRequest')): ?>
		<li>
			<form id="formLoginOpenId" method="post" action="<?php echo $this->here; ?>">
				<fieldset>
					<label>
						<?php echo sprintf(__('Your %s OpenID', true), '<img src="' . Router::url('/images/openid_small.gif') . '" alt="OpenID logo" /> '); ?>
					</label>
					<?php echo $form->input('Identity.openid', array('label' => false, 
										'error' => array('invalid_openid'         => __('Invalid OpenID', true),
														 'verification_cancelled' => __('Verification cancelled', true),
														 'openid_failure'         => sprintf(__('OpenID authentication failed: %s',true), @$errorMessage)))); ?>
					<br />
					<?php echo $form->checkbox('Identity.remember', array('id' => 'IdentityRememberOpenID')); ?>&nbsp;<?php __('Remember me'); ?>
					<br />
					<input type="submit" value="<?php __('Login'); ?>"/>
				</fieldset>
			</form>
		</li>
	<?php endif; ?>
	<li>
		<?php echo $html->link(__('Forgot your password?', true), '/pages/password/recovery/'); ?>
	</li>
	<li>
    	<?php if(Context::read('network.registration_type') == 1): ?>
    	    <?= $html->link(__('Register a new account', true), '/pages/register/') ?>
    	<?php endif; ?>
	</li>
</ul>
