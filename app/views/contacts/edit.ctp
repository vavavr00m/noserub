<?php $flashmessage->render(); ?>
<form id="DefineContactTypesForm" method="post" action="<?php echo $this->here ?>">
	<?php foreach ($noserubContactTypes as $contactType): ?>
		<?php echo $form->checkbox('NoserubContactType.'.$contactType['NoserubContactType']['id'], array('checked' => in_array($contactType['NoserubContactType']['id'], $selectedNoserubContactTypes))); ?>
		<?php echo $contactType['NoserubContactType']['name']; ?>
		<br />
	<?php endforeach; ?>
	<div>
	Selected tags:
	<?php foreach ($selectedContactTypes as $contactType): ?>
		<?php echo $contactType['ContactType']['name']; ?>
	<?php endforeach; ?>
	</div>
	<div>
	Your tags:
	<?php foreach ($contactTypes as $contactType): ?>
		<?php echo $contactType['ContactType']['name']; ?>
	<?php endforeach; ?>
	</div>
	<input class="submitbutton" type="submit" name="submit" value="Save"/>
<?php echo $form->end(); ?>