<h1>Login</h1>
<?php if(isset($form_error) && !empty($form_error)) { 
    echo '<p>'. $form_error . '</p>';
} ?>
<?php echo $form->create(null, array('action' => $this->action)); ?>
    <fieldset>
        <?php echo $form->input('Identity.username'); ?>
        <?php echo $form->input('Identity.password', array('type' => 'password')); ?>
        <?php echo $form->submit('Login'); ?>
    </fieldset>
<?php echo $form->end(); ?>
