<h2>Export your account data</h2>
<p id="message" class="info">
    Export all the information we have about you. That means all your
    profile information, your accounts and your contacts. You can use
    this data on another NoseRub installation to import your data.
</p>
<form id="IdentityAccountSettingsExportForm" method="post" action="<?php echo $this->here . 'export/'; ?>">
    <fieldset>
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    </fielset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Export all my data"/>
    </fieldset>
</form>

<h2>Import your account data</h2>
<p id="message" class="info">
    Import previously exported NoseRub data, so you have all your data
    from the old NoseRub ID on this one.
</p>
<form id="IdentityAccountSettingsImportForm" method="post" action="<?php echo $this->here . 'import/'; ?>">
    <fieldset>
        <div class="input">
            <?php echo $form->file('Identity.import'); ?>
        </div>
        <div class="input">
            <?php echo $form->checkbox('Import.overwrite'); ?> <span>Delete all previous data</span>
        </div>
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    </fielset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Import all data"/>
    </fieldset>
</form>

<h2>Leave a moving address</h2>
<p id="message" class="info">
    If you no longer need this NoseRub ID, but want to make sure you new page
    can be accessed through this URL, just enter one. Visitors will be redirected
    to that URL then.
</p>
<form id="IdentityAccountSettingsRedirectForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>Redirect URL</legend>
        <div class="input">
            <?php echo $form->input('Identity.redirect', array('label' => false, 'size' => 64)); ?>
        </div>
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    </fielset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Save"/>
    </fieldset>
</form>

<h2>Delete your account</h2>
<p id="message" class="warning">
    Once you deleted your account, you can not gain it back again.<br />
    <strong>Your current username will be blocked on this server in order to prevent fraud.</strong><br />
    Make sure you exported your data before and set a redirect URL (See above).
</p>
<form id="IdentityAccountSettingsForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>Delete Account</legend>
        <?php if(isset($confirm_error)) { ?>
            <div id="message" class="warning">
                <p>
                    <?php echo $confirm_error; ?>
                </p>
            </div>
        <?php } ?>
        
        <div class="input">
            <?php echo $form->checkbox('Identity.confirm'); ?> <span><strong>Yes, please delete my account.</strong></span>
        </div>
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    </fieldset>
    
    <fieldset>
        <input class="submitbutton" type="submit" value="Delete account"/>
    </fieldset>
</form>