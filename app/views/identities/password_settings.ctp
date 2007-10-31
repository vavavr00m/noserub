<?php $flashmessage->render(); ?>
<p class="infotext">
    In order to change your password, you have to enter your current password and then the new one.
</p>
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
                                                           'error' => 'Password is not correct')); 
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
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    </fieldset>
    
    <fieldset>
        <input class="submitbutton" type="submit" value="Save changes"/>
    </fieldset>
</form>