<form id="IdentityRegisterForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php 
            echo $form->input('Identity.username', 
                              array('error' => array(
                                    'required' => __('You need to enter something here. Valid characters: letters ,numbers, underscores, dashes and dots', true),
                                    'content'  => __('Valid characters: letters, numbers, underscores, dashes and dots only', true),
                                    'unique'   => __('The username is already taken', true)))); 
        ?>
        <?php 
            echo $form->input('Identity.email', array('label' => __('E-Mail (validation link will be sent there)', true), 
                                                      'error' => __('Please enter a valid E-Mail address', true))); 
        ?>
    </fieldset>
    <p>
        <?php __('You can change the privacy settings everytime you like. Just go to the Settings, once you are logged in.'); ?>
    </p>
    <?php echo $this->element('identities/privacy_settings'); ?>
    <fieldset>
        <input class="submitbutton" type="submit" value="<?php __('Register'); ?>"/>
    </fieldset>
</form>