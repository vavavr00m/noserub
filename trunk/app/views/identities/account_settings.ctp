<p>
    Once you deleted your account, you can not gain it back again. The chosen username will
    be blocked on this server in order to prevent fraud.
</p>
<form id="IdentityAccountSettingsForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php if(isset($confirm_error)) { ?>
            <div id="message" class="warning">
                <p>
                    <?php echo $confirm_error; ?>
                </p>
            </div>
        <?php } ?>
        <?php echo $form->checkbox('Identity.confirm'); ?>&nbsp;<strong>Yes, please delete my account.</strong>
        <?php 
            $openid = $session->read('Identity.openid');
        	# if the user registered with an OpenID then there is no password available and so we don't show the password field
            if (!isset($openid)) {
        		echo $form->input('Identity.passwd', array('type'  => 'password',
                	                                       'label' => 'Password', 
                    	                                   'error' => 'You need to enter your password to confirm the complete deletion of your account.'));
            } 
        ?>
    </fieldset>
    
    <fieldset>
        <input class="submitbutton" type="submit" value="Delete account"/>
    </fieldset>
</form>