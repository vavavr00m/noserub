<div id="bd-main" class="with-sidebar">
    <div id="bd-main-hd">
	</div>
	<div id="bd-main-bd">
        <h2><?php __('Export your account data'); ?></h2>
        <p class="infotext">
            <?php __('Export all the information we have about you. That means all your profile information, your accounts and your contacts. You can use this data on another NoseRub installation to import your data.'); ?>
        </p>
        <p>
            <a href="<?php echo $this->here . 'export/' . $noserub->fnSecurityToken(); ?>"><?php __('Export all my data'); ?></a>
        </p>

        <h2><?php __('Import your account data'); ?></h2>
        <p class="infotext">
            <?php __('Import previously exported NoseRub data, so you have all your data from the old NoseRub ID on this one.'); ?>
        </p>

        <form id="IdentityAccountSettingsImportForm" enctype="multipart/form-data" method="post" action="<?php echo $this->here . 'import/'; ?>">
            <fieldset>
                <legend><?php __('Import NoseRub Data'); ?></legend>
                <input type="file" name="data[Import][data]" />
                <?php echo $noserub->fnSecurityTokenInput(); ?>
            </fieldset>

            <fieldset>
                <input class="submitbutton" type="submit" value="<?php __('Import all data'); ?>" />
            </fieldset>
        </form>

        <h2><?php __('Leave a moving address'); ?></h2>
        <p id="message" class="info">
            <?php __('If you no longer need this NoseRub ID, but want to make sure your new page can be accessed through this URL, just enter one.<br />Visitors will be redirected to that URL then.'); ?>
        </p>
        <form id="IdentityAccountSettingsRedirectForm" method="post" action="<?php echo $this->here . 'redirect/'; ?>">
            <fieldset>
                <legend><?php __('Redirect URL'); ?></legend>
                <label for="IdentityRedirect"><?php __('Please add the full URL (http://)'); ?></label>
                <p><?php echo $form->input('Identity.redirect_url', array('label' => false, 'size' => 64)); ?></p>
                <?php echo $noserub->fnSecurityTokenInput(); ?>
            </fieldset>
    
            <fieldset>
                <input class="submitbutton" type="submit" value="<?php __('Save'); ?>"/>
            </fieldset>
        </form>

        <h2><?php __('Delete your account'); ?></h2>
        <p id="message" class="warning">
            <?php __('Once you deleted your account, you can not gain it back again.<br /><strong>Your current username will be blocked on this server in order to prevent fraud.</strong><br />Make sure you exported your data before and set a redirect URL (See above).'); ?>
        </p>
        <form id="IdentityAccountSettingsForm" method="post" action="<?php echo $this->here; ?>">
            <fieldset>
                <legend><?php __('Delete Account'); ?></legend>
                <?php if(isset($confirm_error)) { ?>
                    <div id="message" class="warning">
                        <p>
                            <?php echo $confirm_error; ?>
                        </p>
                    </div>
                <?php } ?>
        
                <div class="input">
                    <label for="IdentityConfirm_"><?php __('Are you sure?'); ?></label>
                    <?php echo $form->checkbox('Identity.confirm'); ?> <span><strong><?php __('Yes, please delete my account.'); ?></strong></span>
                </div>
                <?php echo $noserub->fnSecurityTokenInput(); ?>
            </fieldset>
    
            <fieldset>
                <input class="submitbutton" type="submit" value="<?php __('Delete account'); ?>"/>
            </fieldset>
        </form>
    </div>
     <div id="bd-main-sidebar">
    		<?php echo $noserub->widgetSettingsNavigation(); ?>
    	</div>
    </div>