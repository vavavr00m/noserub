<form id="IdentityPassowrdSettingsForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php if(isset($paswd_error)) { ?>
            <p>
                <?php echo $passwd_error; ?>
            </p>
        <?php } ?>
        <?php 
            echo $form->input('Identity.old_passwd', array('type'  => 'password',
                                                           'label' => 'Old password', 
                                                           'error' => 'Passwords must be at least 6 characters in length')); 
        ?>
        <?php 
            echo $form->input('Identity.passwd', array('type'  => 'password',
                                                       'label' => 'New password', 
                                                       'error' => 'Passwords must be at least 6 characters in length')); 
        ?>
        <?php 
            echo $form->input('Identity.passwd2', array('type' => 'password', 
                                                        'label' => 'New password repeated', 
                                                        'error' => 'Passwords must match')); ?>
    </fieldset>
    
    <fieldset>
        <?php echo $form->submit('Save changes'); ?>
    </fieldset>
<?php echo $form->end(); ?>