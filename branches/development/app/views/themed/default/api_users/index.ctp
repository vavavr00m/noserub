<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	</div>
	<div id="bd-main-bd">
		<p class="infotext">
			<?php __('In order to use the API, you have to define username/password or use <a href="../oauth">OAuth</a> for this purpose.'); ?>
		</p>
		<hr class="space" />
		<div class="left">
			<form id="ApiUserSettingsForm" method="post" action="<?php echo $this->here; ?>">
				<fieldset>
					<?php 
						echo $form->input('ApiUser.username', array('label' => __('Username', true), 
																	'error' => __('Password is not correct', true))); 
						echo $form->input('ApiUser.password', array('type'  => 'password',
																	'label' => __('Password', true), 
																	'error' => __('Passwords must be at least 6 characters in length', true))); 
						echo $form->input('ApiUser.password2', array('type' => 'password', 
																	 'label' => __('Password repeated', true), 
																	 'error' => __('Passwords must match', true)));
						echo $noserub->fnSecurityTokenInput(); 
					?>
				</fieldset>
				<fieldset>
					<input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
				</fieldset>
			</form>
		</div>
	</div>
	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetSettingsNavigation(); ?>
	</div>
</div>