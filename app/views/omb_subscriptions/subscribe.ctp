<?php $flashmessage->render(); ?>
<form action="<?php echo $this->here; ?>" method="post">
	<?php echo $form->input('Omb.url', array('label' => 'URL of your profile')); ?>
	<?php echo $form->submit('Subscribe'); ?>
</form>