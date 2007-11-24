<form id="DefineContactTypesForm" method="post" action="<?php echo $this->here ?>">
	<?php foreach ($noserubContactTypes as $contactType): ?>
		<?php echo $form->checkbox('NoserubContactType.'.$contactType['NoserubContactType']['id']); ?>
		<?php echo $contactType['NoserubContactType']['name']; ?>
		<br />
	<?php endforeach; ?>
	<input class="submitbutton" type="submit" name="submit" value="Save"/>
	<input class="submitbutton" type="submit" name="cancel" value="Skip this step"/>
<?php echo $form->end(); ?>