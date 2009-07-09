<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<?php echo $noserub->widgetProfile(); ?>
	</div>
	<div id="bd-main-bd">
	    <h2><? __('Add new contact'); ?></h2>
		<p>
			<?php __("You can either enter an URL or a NoseRub-ID (noserubserver.com/MyBuddy). Naturally you would want to add a contact's blog as an URL, or his/her FriendFeed-URL."); ?>
		</p>
		<?php 
			echo $form->create('Contact', array('add'));
			echo $form->input('Contact.noserub_id', 
							  array('label' => __('NoseRub-ID / URL', true),
									'size'  => 32,
									'error' => array(
										'user_not_found'      => __('This user could not be found at the other server', true),
										'no_valid_noserub_id' => __('This is no valid NoseRub-ID or URL!', true),
										'own_noserub_id'      => __('You cannot add yourself as a contact.', true),
										'unique'              => __('This user has already been added as a contact.', true)))); 
			echo $form->end(array('label' => __('Add', true)));
			echo $form->end(); 
		?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetContacts(); ?>
	</div>
</div>
