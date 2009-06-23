<div id="bd-main-hd">
</div>
<div id="bd-main-bd">
    <?php echo $noserub->widgetFlashMessage(); ?>
    <?php	if(!isset($session_identity["openid"])){	?>
    <p class="infotext">
    	<?php __('In order to change your password, you have to enter your current password and then the new one.'); ?>
    </p>
    <?php } ?>

    <hr class="space" />

    <!-- API Box -->
    <div id="locationsapi" class="<?php if(!isset($session_identity["openid"])){ ?>right<?php }else{?>left<?php } ?>">
        <form id="APISettingsForm" method="post" action="<?php echo $this->here; ?>">
        	<input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
        	<fieldset>
        		<legend><?php __('API Settings'); ?></legend>
        		<p class="infotext">
                    <?php echo sprintf(__('For more information, please check out our API-Documentation: %s', true), '<a href="http://noserub.com/documentation/archives/API-Application-Programming-Interface.html">noserub.com/documentation</a>'); ?>
            	</p>
        		<input type="checkbox" name="data[Identity][api_active]" 
        			<?php if ($session_identity['api_active'] == 1) { ?>
                    	checked="checked"
                    <?php } ?> 
                    value = '1' /> <?php __('API activated'); ?>	
        		<?php echo $form->input('Identity.api_hash', array('label' => __('API hash', true), 'value' => $session_identity['api_hash'])); ?>
                <input class="submitbutton" name="api" type="submit" value="<?php __('Save changes'); ?>"/>
        	</fieldset>
        <?php echo $form->end(); ?>
    </div>
    <?php
    	if(!isset($session_identity["openid"])){
    ?>
    <div class="left">
        <form id="IdentityPassowrdSettingsForm" method="post" action="<?php echo $this->here; ?>">
            <fieldset>
                <legend><?php __('Change your password'); ?></legend>
                <?php if(isset($paswd_error)) { ?>
                    <p>
                        <?php echo $passwd_error; ?>
                    </p>
                <?php } ?>
                <?php 
                    echo $form->input('Identity.old_passwd', array('type'  => 'password',
                                                                   'label' => __('Old password', true), 
                                                                   'error' => __('Password is not correct', true))); 
                ?>
                <?php 
                    echo $form->input('Identity.passwd', array('type'  => 'password',
                                                               'label' => __('New password', true),
                                                               'error' => __('Passwords must be at least 6 characters in length', true))); 
                ?>
                <?php 
                    echo $form->input('Identity.passwd2', array('type' => 'password', 
                                                                'label' => __('New password repeated', true), 
                                                                'error' => __('Passwords must match', true))); ?>
                <input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
            </fieldset>
    
            <fieldset>
                <input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
            </fieldset>
        </form>
    </div>
    <?php	}	?>
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>