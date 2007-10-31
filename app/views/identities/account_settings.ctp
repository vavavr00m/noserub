<p class="infotext">
    Once you deleted your account, you can not gain it back again.<br />
    <strong>Your current username will be blocked on this server in order to prevent fraud.</strong>
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
        
        <div class="input">
        <label>Delete Account</label>
        <?php echo $form->checkbox('Identity.confirm'); ?> <span><strong>Yes, please delete my account.</strong></span>
        </div>
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    </fieldset>
    
    <fieldset>
        <input class="submitbutton" type="submit" value="Delete account"/>
    </fieldset>
</form>