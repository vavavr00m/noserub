<?php $flashmessage->render(); ?>
<p class="infotext">
    In order to change your password, you have to enter your current password and then the new one.
</p>

<hr class="space" />

<!-- API Box -->
<div id="locationsapi" class="right">
    <form id="APISettingsForm" method="post" action="<?php echo $this->here; ?>">
    	<input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    	<fieldset>
    		<legend>API Settings</legend>
    		<p class="infotext">
                For more information, please check out our API-Documentation: <a href="http://noserub.com/documentation/archives/API-Application-Programming-Interface.html">noserub.com/documentation</a>
        	</p>
    		<input type="checkbox" name="data[Identity][api_active]" 
    			<?php if ($session_identity['api_active'] == 1) { ?>
                	checked="checked"
                <?php } ?> 
                value = '1' /> API activated	
    		<?php echo $form->input('Identity.api_hash', array('label' => 'API hash', 'value' => $session_identity['api_hash'])); ?>
            <input class="submitbutton" name="api" type="submit" value="Save changes"/>
    	</fieldset>
    <?php echo $form->end(); ?>
</div>

<div class="left">
    <form id="IdentityPassowrdSettingsForm" method="post" action="<?php echo $this->here; ?>">
        <fieldset>
            <legend>Change your password</legend>
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
</div>