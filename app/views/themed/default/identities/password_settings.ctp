<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php if(!isset($session_identity["openid"])):	?>
			<p class="infotext">
				<?php __('In order to change your password, you have to enter your current password and then the new one.'); ?>
			</p>
			<hr class="space" />
			<div class="left">
				<form id="IdentityPasswordSettingsForm" method="post" action="<?php echo $this->here; ?>">
					<fieldset>
						<legend><?php __('Change your password'); ?></legend>
						<?php if(isset($paswd_error)) { ?>
							<p>
								<?php echo $passwd_error; ?>
							</p>
						<?php } ?>
						<?php 
							echo $form->input('Identity.old_passwd', array('type'  => 'password',
																		   'label' => __('Old password', true), 
																		   'error' => __('Password is not correct', true))); 
						?>
						<?php 
							echo $form->input('Identity.passwd', array('type'  => 'password',
																	   'label' => __('New password', true),
																	   'error' => __('Passwords must be at least 6 characters in length', true))); 
						?>
						<?php 
							echo $form->input('Identity.passwd2', array('type' => 'password', 
																		'label' => __('New password repeated', true), 
																		'error' => __('Passwords must match', true))); ?>
						<?php echo $noserub->fnSecurityTokenInput(); ?>
					</fieldset>
			
					<fieldset>
						<input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
					</fieldset>
				</form>
			</div>
		<?php endif; ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetSettingsNavigation(); ?>
	</div>
</div>
