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
    </fieldset>
    <p>
        You can change the following privacy settings everytime you like. Just 
        go to the Settings, once you are logged in.
    </p>
    <?php echo $this->element('identities/privacy_settings'); ?>
    <fieldset>
        <input class="submitbutton" type="submit" value="Register"/>
    </fieldset>
</form>