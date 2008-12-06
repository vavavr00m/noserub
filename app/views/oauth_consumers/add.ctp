<form id="ConsumerAddForm" method="post" action="<?php echo $this->here; ?>">
	<fieldset>
		<legend>
			<?php __('Register your application'); ?>
		</legend>
		<?php
		echo $form->create('Consumer');
		echo $form->input('application_name', array('error' => __('You need to specify an application name.', true)));
		echo $form->input('callback_url', array('label' => __('Callback Url (optional)', true), 'error' => __('You need to specify a valid url', true)));
		echo $form->submit(__('Save', true), array('class' => 'submitbutton'));
		?>
	</fieldset>
</form>