<?php /* if (!$session->check('Noserub.lastOpenIDRequest')): ?>
	<p class="infotext">
		<?php echo $html->link('Login', '/pages/login/withopenid'); ?> with your <img src="<?php echo Router::url('/images/openid_small.gif'); ?>" alt="OpenID logo" /> OpenID.
	</p>
<?php endif; */ ?>
<?php if(isset($form_error) && !empty($form_error)) { 
    echo '<p>'. $form_error . '</p>';
} ?>
<?php if (!$session->check('Noserub.lastOpenIDRequest')): ?>
	<div class="boxRight right">
		<form id="IdentityPagesLoginOpenIDForm" method="post" action="<?php echo $this->here; ?>">
			<fieldset>
		    	<label>Your <img src="<?php echo Router::url('/images/openid_small.gif'); ?>" alt="OpenID logo" /> OpenID</label>
		    	<?php echo $form->input('Identity.openid', array('label' => false, 
								    'error' => array('invalid_openid' => 'Invalid OpenID',
													 'verification_cancelled' => 'Verification cancelled',
													 'openid_failure' => 'OpenID authentication failed: '.@$errorMessage))); ?>
		    	<br />
		        <?php echo $form->checkbox('Identity.remember', array('id' => 'IdentityRememberOpenID')); ?>Remember me
		        <br />
		        <input class="submitbutton" type="submit" value="Login"/>
		    </fieldset>
		</form>
	</div>
<?php endif; ?>
<div class="boxLeft">
	<form id="IdentityPagesLoginForm" method="post" action="<?php echo $this->here; ?>">
	    <fieldset>
	        <?php echo $form->input('Identity.username'); ?>
	        <?php echo $form->input('Identity.password', array('type' => 'password')); ?>
	    	<br />
	        <?php echo $form->checkbox('Identity.remember'); ?>Remember me
	        <br />
	        <input class="submitbutton" type="submit" value="Login"/>
	    </fieldset>
	<?php echo $form->end(); ?>
</div>
