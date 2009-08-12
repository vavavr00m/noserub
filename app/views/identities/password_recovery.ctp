<div id="bd-main">
	<div id="bd-main-hd">
		<h1><?php __('Password recovery'); ?></h1>
	</div>
	<div id="bd-main-bd">
		<p>
			<?php __('Please enter username or email address. We will then send you an email with a link to set a new password.'); ?>
		</p>
		<?php echo $form->create('Identity', array('url' => '/pages/password/recovery', 'id' => 'PasswordRecoveryForm')); ?>
		<?php echo $form->input('Identity.username', array('label' => __('Username', true))); ?>
		<?php echo $form->input('Identity.email', array('label' => __('E-Mail', true))); ?>
		<?php echo $form->end(__('Send me the link', true)); ?>
	</div>
</div>
