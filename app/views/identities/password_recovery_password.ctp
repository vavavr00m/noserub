<p>
    <?php __('Please enter your new password (twice). It needs to be at least 6 characters long.'); ?>
</p>
<?php echo $form->create('Identity', array('url' => '/pages/password/set/' . $recovery_hash)); ?>
    <fieldset>
        <?php echo $form->input('Identity.password', array('label' => __('Password', true), 'type' => 'password')); ?>
        <?php echo $form->input('Identity.password2', array('label' => __('Password (repeated)', true), 'type' => 'password')); ?>
    </fieldset>
<?php echo $form->end(__('Save new password', true)); ?>