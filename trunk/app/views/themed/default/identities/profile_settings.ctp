<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<form id="IdentitySettingsForm" enctype="multipart/form-data" method="post" action="<?php echo $this->here; ?>">
			<?php echo $noserub->fnSecurityTokenInput(); ?>
			<div id="settings_photo" class="right">
				<fieldset>
					<legend><?php __('Photo'); ?></legend>
					<?php if($this->data['Identity']['photo']) { ?>
						<p>
							<strong><?php __('Your current photo'); ?>:</strong><br />
							<?php 
								$photo = $this->data['Identity']['photo'];
								if(strpos($photo, 'http') === 0) {
									$url = $photo;
								} else {
									$url = FULL_BASE_URL . Router::url('/static/avatars/'.$this->data['Identity']['photo'].'.jpg');
								}
							?>
							<img src="<?php echo $url; ?>" width="150" height="150" alt="<?php __('Your current photo'); ?>" class="mypicture" />
						</p>
						<p>
							<input type="checkbox" name="data[Identity][remove_photo]" value="1"><?php __('Remove photo'); ?>
						</p>
					<?php } ?>
					<p>
						<?php echo sprintf(__("Size may not exceed 150x150 pixels. If you don't have one with the right size, try %s.<br />GIF, JPG and PNG allowed.", true), '<a href="http://mypictr.com/?size=150x150">myPictr.com</a>'); ?>
					</p>
					<label><?php __('Photo/Portrait'); ?>:</label>
					<input type="file" name="data[Identity][photo]" />
					<?php if(!$this->data['Identity']['photo']) { ?>
						<br />
						<?php echo $form->checkbox('Identity.use_gravatar'); ?><?php __('load from Gravatar.com'); ?>
					<?php } ?>
					<p><input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/></p>
				</fieldset>
			</div>
		
			<div id="settings_data">
		
			<fieldset>
				<legend><?php __('Personal data'); ?></legend>
				<?php 
					echo $form->input('Identity.firstname', array('label' => __('Your firstname', true),
																  'size'  => 32)); 
				?>
				<?php 
					echo $form->input('Identity.lastname', array('label' => __('Your lastname', true),
																  'size'  => 32)); 
				?>
				<label><?php __('Sex'); ?></label>
				<input type="radio" name="data[Identity][sex]" value="0"<?php echo $this->data['Identity']['sex'] == 0 ? ' checked="checked"' : ''; ?>> <span><?php __('rather not say'); ?></span>
				<input type="radio" name="data[Identity][sex]" value="1"<?php echo $this->data['Identity']['sex'] == 1 ? ' checked="checked"' : ''; ?>> <span><?php __('female'); ?></span>
				<input type="radio" name="data[Identity][sex]" value="2"<?php echo $this->data['Identity']['sex'] == 2 ? ' checked="checked"' : ''; ?>> <span><?php __('male'); ?></span>
			    <?php echo $form->input('Identity.title', array('label' => 'Title (for profile)')); ?>
			    <?php echo $form->input('Identity.keywords', array('label' => 'Keywords (for META Tag)')); ?>
			</fieldset>
		
			<fieldset>
				<legend><?php __('Make a statement'); ?></legend>
				<p>
					<?php __('HTML is not allowed; newlines are preserved; URLs with http:// and https:// will turn into links'); ?>
				</p>
				<?php echo $form->textarea('Identity.about', array('label' => __('About', true))); ?>
			</fieldset>
		
			<fieldset>
				<legend><?php __('Geolocation'); ?></legend>
				<?php 
					echo $form->input('Identity.address', array('label' => __('Address for geolocation (<strong>private</strong>)', true),
																'size'  => 64)); 
				?>
			
				<div id="geolocation_preview">
					<p class="geolocation"><?php __('Latitude'); ?><br /><strong><?php echo $this->data['Identity']['latitude']; ?></strong></p>
					<p class="geolocation"><?php __('Longitude'); ?><br /><strong><?php echo $this->data['Identity']['longitude']; ?></strong></p>
				</div>

				<p>
					<?php __('The address is used to determine the geolocation. This address will <strong>not</strong> be displayed to anyone else, just the geolocation, if you enter a valid address.'); ?>
				</p>
		   
				<?php 
					echo $form->input('Identity.address_shown', array('label' => __('Address for your profile (<strong>public</strong>)', true),
																	  'size'  => 64)); 
				?>
				<p>
					<?php __('Here you can specify, how you would like your address to be displayed. For instance: <strong>Paris, France</strong><br />
					If you leave it empty, nothing will be shown on your profile page.'); ?>
				</p>

			</fieldset>
		
			<fieldset>
				<input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
			</fieldset>
		<?php echo $form->end(); ?>

		</div>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetSettingsNavigation(); ?>
	</div>
</div>
