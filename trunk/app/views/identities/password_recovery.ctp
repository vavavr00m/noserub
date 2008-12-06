<?php $flashmessage->render(); ?>
<p>
    <?php __('Please enter username or password. We will then send you an email with a link to set a new password.'); ?>
</p>
<?php echo $form->create('Identity', array('url' => '/pages/password/recovery')); ?>
    <fieldset>
        <?php echo $form->input('Identity.username', array('label' => __('Username', true))); ?>
        <?php echo $form->input('Identity.email', array('label' => __('E-Mail', true))); ?>
    </fieldset>
<?php echo $form->end(__('Send me the link', true)); ?>