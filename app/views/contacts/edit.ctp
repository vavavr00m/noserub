<?php foreach ($noserubContactTypes as $contactType): ?>
	<?php echo $form->checkbox('NoserubContactType.'.$contactType['NoserubContactType']['id'], array('checked' => in_array($contactType['NoserubContactType']['id'], $selectedNoserubContactTypes))); ?>
	<?php echo $contactType['NoserubContactType']['name']; ?>
	<br />
<?php endforeach; ?>