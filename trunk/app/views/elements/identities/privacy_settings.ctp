<fieldset>
    <legend>Updates</legend>
    <p class="infotext">
        We want to show not logged in users, what is going on inside. 
        So, if you give us your permisson, we will show your updates
        there for everyone.
    </p>
    <input type="radio" name="data[Identity][frontpage_updates]" value="1"<?php echo $this->data['Identity']['frontpage_updates'] == 1 ? ' checked="checked"' : ''; ?>> <span>show my updates on frontpage</span>
    <input type="radio" name="data[Identity][frontpage_updates]" value="0"<?php echo $this->data['Identity']['frontpage_updates'] == 0 ? ' checked="checked"' : ''; ?>> <span>don't show them</span>
</fieldset>