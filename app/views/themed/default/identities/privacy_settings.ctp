<div id="bd-main-hd">
</div>
<div id="bd-main-bd">    <form id="IdentityPrivacySettingsForm" method="post" action="<?php echo $this->here; ?>">
        <?php echo $this->element('identities/privacy_settings'); ?>
        <input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
        <fieldset>
            <input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
        </fieldset>
    </form>

    <p id="message" class="info">
        <?php echo sprintf(__('If you would like to discuss what and how to save from public
        viewing, please join us at %s.', true), '<a href="http://noserub.com/discuss/">noserub.com/discuss</a>'); ?>
    </p>
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>