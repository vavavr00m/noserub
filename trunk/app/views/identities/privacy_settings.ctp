<form id="IdentityPrivacySettingsForm" method="post" action="<?php echo $this->here; ?>">
    <?php echo $this->renderElement('identities/privacy_settings'); ?>
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
        <input class="submitbutton" type="submit" value="Save changes"/>
    </fieldset>
</form>
<p>
    If you would like to discuss what and how to save from public
    viewing, please join us at <a href="http://noserub.com/discuss/">noserub.com/discuss</a>.<br />
    <br />
    Thank you!
</p>