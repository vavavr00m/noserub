<h1>Register</h1>
<?php echo $form->create('Identity', array('action' => $this->action)); ?>
    <fieldset>
        <?php 
            echo $form->input('Identity.username', 
                              array('error' => array(
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes and dots',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes and dots only',
                                    'unique'   => 'The username is alreay taken'))); 
        ?>
        <?php 
            echo $form->input('Identity.email', array('label' => 'E-Mail (validation link will be sent there)', 
                                                      'error' => 'Please enter a valid E-Mail address')); 
        ?>
        <?php 
            echo $form->input('Identity.passwd', array('type'  => 'password',
                                                       'label' => 'Password', 
                                                       'error' => 'Passwords must be at least 6 characters in length')); 
        ?>
        <?php 
            echo $form->input('Identity.passwd2', array('type' => 'password', 
                                                        'label' => 'Password repeated', 
                                                        'error' => 'Passwords must match')); ?>
        <?php echo $form->submit('Register'); ?>
    </fieldset>
<?php echo $form->end(); ?>