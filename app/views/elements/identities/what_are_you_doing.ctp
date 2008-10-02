<h4>What are you doing? (<strong id="MicropublishCount">140</strong> chars left)</h4>
<form action="<?php echo $this->here; ?>" method="post">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
        <div class="input micropublish">
            <?php echo $form->textarea('Micropublish.value'); ?>
            <input class="submitbutton" type="submit" value="Send"/>
        </div>
    </fieldset>
</form>