<form id="IdentityPrivacySettingsForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>Updates</legend>
        <p>
            We want to show not logged in users, what is going on inside. 
            So, if you give us your permisson, we will show your updates
            there for everyone.
        </p>
        <input type="radio" name="data[Identity][frontpage_updates]" value="1"<?php echo $this->data['Identity']['frontpage_updates'] == 1 ? ' checked="checked"' : ''; ?>> <span>show my updates on frontpage</span>
        <input type="radio" name="data[Identity][frontpage_updates]" value="0"<?php echo $this->data['Identity']['frontpage_updates'] == 0 ? ' checked="checked"' : ''; ?>> <span>don't show them</span>
    </fieldset>

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