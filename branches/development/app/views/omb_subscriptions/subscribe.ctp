<?php $flashmessage->render(); ?>
<form action="<?php echo $this->here; ?>" method="post">
	<?php echo $form->input('Omb.url', array('label' => __('URL of your profile on another compatible microblogging service', true))); ?>
	<?php echo $form->submit(__('Subscribe', true)); ?>
</form>