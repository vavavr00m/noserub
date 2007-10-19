<p class="infotext">
If you have an <img src="/images/openid_small.gif" alt="OpenID logo" /> OpenID, please follow this 
<?php echo $html->link('link', '/pages/register/withopenid'); ?> to register a new NoseRub account.
</p>

<form id="IdentityRegisterForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php 
            echo $form->input('Identity.username', 
                              array('error' => array(
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes and dots',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes and dots only',
                                    'unique'   => 'The username is already taken'))); 
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
                                                        'error' => 'Passwords must match')); 
        ?>
    </fieldset>

    <?php echo $this->renderElement('identities/privacy_settings'); ?>
        <p class="infotext">
        (You can change the privacy settings everytime you like. Just go to the Settings, once you are logged in.)
    </p>

    <fieldset>
        <input class="submitbutton" type="submit" value="Register"/>
    </fieldset>
</form>