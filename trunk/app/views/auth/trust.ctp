<p>
A site identifying as <?php echo $trustRoot; ?> has asked us for confirmation that <?php echo $identity; ?> is your identity URL.
</p>
<form method="post" action="<?php echo $this->here; ?>">
    <input class="submitbutton" type="submit" name="Allow" value="allow" />
    <input class="submitbutton" type="submit" name="Deny" value="deny" />
</form>