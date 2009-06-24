<div>
    <?php echo $form->create(array('url' => '/pages/login/')); ?>
	    <fieldset>
	        <?php echo $form->input('Identity.username'); ?>
	        <?php echo $form->input('Identity.password', array('type' => 'password')); ?>
	    	<br />
	        <?php echo $form->checkbox('Identity.remember'); ?><?php __('Remember me'); ?>
	        <br />
	        <input class="submitbutton" type="submit" value="<?php __('Login'); ?>"/>
	    </fieldset>
	<?php echo $form->end(); ?>

	<?php echo $html->link(__('Forgot your password?', true), '/pages/password/recovery/'); ?>
</div>
<?php if(!$session->check('Noserub.lastOpenIDRequest')): ?>
	<div>
		<form id="IdentityPagesLoginOpenIDForm" method="post" action="<?php echo $this->here; ?>">
			<fieldset>
		    	<label>
		    	    <?php echo sprintf(__('Your %s OpenID', true), '<img src="' . Router::url('/images/openid_small.gif') . '" alt="OpenID logo" /> '); ?>
		    	</label>
		    	<?php echo $form->input('Identity.openid', array('label' => false, 
								    'error' => array('invalid_openid'         => __('Invalid OpenID', true),
													 'verification_cancelled' => __('Verification cancelled', true),
													 'openid_failure'         => sprintf(__('OpenID authentication failed: %s',true), @$errorMessage)))); ?>
		    	<br />
		        <?php echo $form->checkbox('Identity.remember', array('id' => 'IdentityRememberOpenID')); ?><?php __('Remember me'); ?>
		        <br />
		        <input class="submitbutton" type="submit" value="<?php __('Login'); ?>"/>
		    </fieldset>
		</form>
	</div>
<?php endif; ?>
