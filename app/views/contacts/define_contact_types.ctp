<form id="DefineContactTypesForm" method="post" action="<?php echo $this->here ?>">
	<?php foreach ($noserubContactTypes as $contactType): ?>
		<?php echo $form->checkbox('NoserubContactType.'.$contactType['NoserubContactType']['id']); ?>
		<?php echo $contactType['NoserubContactType']['name']; ?>
		<br />
	<?php endforeach; ?>
	<?php echo $form->input('ContactType.tags'); ?>
	Your tags:
	<div id="tags">
		<?php foreach ($contactTypes as $contactType): ?>
			<a href="#" onclick="return false;"><?php echo $contactType['ContactType']['name']; ?></a>
		<?php endforeach; ?>
	</div>
	<input class="submitbutton" type="submit" name="submit" value="Save"/>
	<input class="submitbutton" type="submit" name="cancel" value="Skip this step"/>
<?php echo $form->end(); ?>