<form id="ConsumerAddForm" method="post" action="<?php echo $this->here; ?>">
	<fieldset>
		<legend>
			Register your application
		</legend>
		<?php
		echo $form->create('Consumer');
		echo $form->input('application_name', array('error' => 'You need to specify an application name.'));
		echo $form->submit('Save', array('class' => 'submitbutton'));
		?>
	</fieldset>
</form>