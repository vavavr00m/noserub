<?php $flashmessage->render(); ?>
<form id="DefineContactTypesForm" method="post" action="<?php echo $this->here ?>">
	<?php foreach ($noserubContactTypes as $contactType): ?>
		<?php echo $form->checkbox('NoserubContactType.'.$contactType['NoserubContactType']['id'], array('checked' => in_array($contactType['NoserubContactType']['id'], $selectedNoserubContactTypes))); ?>
		<?php echo $contactType['NoserubContactType']['name']; ?>
		<br />
	<?php endforeach; ?>
	<div>
	<?php echo $form->input('ContactType.tags', array('value' => $selectedContactTypes)); ?>
	</div>
	Your tags:
	<div id="tags">
		<?php foreach ($contactTypes as $contactType): ?>
			<a href="#" onclick="return false;"><?php echo $contactType['ContactType']['name']; ?></a>
		<?php endforeach; ?>
	</div>
	<input class="submitbutton" type="submit" name="submit" value="Save"/>
<?php echo $form->end(); ?>