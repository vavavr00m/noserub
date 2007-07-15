<h1>Register</h1>
<?php if(isset($form_error) && !empty($form_error)) { 
    echo '<p>'. $form_error . '</p>';
} ?>
<p>
    Username may only contain letters a to z, numbers, underscore, full stop and dash.
</p>
<?php echo $form->create('Login', array('action' => $this->action)); ?>
    <fieldset>
        <?php 
            echo $form->input('Login.username', 
                              array('error' => array(
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes and dots',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes and dots only',
                                    'unique'   => 'The username is alreay taken'))); 
        ?>
        <?php 
            echo $form->input('Login.passwd', array('type'  => 'password',
                                                    'label' => 'Password', 
                                                    'error' => 'Passwords must be at least 6 characters in length')); 
        ?>
        <?php 
            echo $form->input('Login.passwd2', array('type' => 'password', 
                                                     'label' => 'Password repeated', 
                                                     'error' => 'Passwords must match')); ?>
        <?php echo $form->submit('Register'); ?>
    </fieldset>
<?php echo $form->end(); ?>
