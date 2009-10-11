<div id="bd-main">
	<div id="bd-main-hd">
		<h1><?php __('Register a new account'); ?></h1>
	</div>
	<div id="bd-main-bd">
		<p>
			<?php echo sprintf(__('If you have an %s OpenID, please follow this %s to register a new NoseRub account.', true), '<img src="' . Router::url('/images/openid_small.gif') . '" alt="OpenID logo" />', $html->link(__('link', true), '/pages/register/withopenid')); ?>
		</p>

		<form id="IdentityRegisterForm" method="post" action="<?php echo $this->here; ?>">
			<fieldset>
				<?php 
					echo $form->input('Identity.username', 
									  array('error' => array(
											'required' => __('You need to enter something here. Valid characters: letters ,numbers, underscores, dashes and dots', true),
											'content'  => __('Valid characters: letters, numbers, underscores, dashes and dots only', true),
											'unique'   => __('The username is already taken', true)))); 
				?>
				<?php 
					echo $form->input('Identity.email', array('label' => __('E-Mail (validation link will be sent there)', true), 
															  'error' => __('Please enter a valid E-Mail address', true))); 
				?>
				<?php 
					echo $form->input('Identity.passwd', array('type'  => 'password',
															   'label' => __('Password', true), 
															   'error' => __('Passwords must be at least 6 characters in length', true))); 
				?>
				<?php 
					echo $form->input('Identity.passwd2', array('type' => 'password', 
																'label' => __('Password repeated', true), 
																'error' => __('Passwords must match', true))); 
				?>
			</fieldset>

			<?php echo $this->element('identities/privacy_settings'); ?>
			<p>
				(<?php __('You can change the privacy settings everytime you like. Just go to the Settings, once you are logged in.'); ?>)
			</p>

			<fieldset>
				<input type="submit" value="<?php __('Register'); ?>"/>
			</fieldset>
		</form>
	</div>
</div>
