<?php $flashmessage->render(); ?>
<div style="float:left;">
    <?php echo $this->renderElement('accounts/index'); ?>
</div>
<div class="left" style="float:right;">
    <form>
        <fieldset>
            <?php foreach($contact_accounts as $account) { ?>
                <img class="whoisicon" alt="<?php echo $account['Service']['name']; ?>" src="/images/icons/services/<?php echo $account['Service']['icon']; ?>"/>
                <?php echo $account['Service']['name']; ?>
                <input type="text" size="32">
                <br />
            <?php } ?>
        </fieldset>
        <fieldset>
            <input class="submitbutton" type="submit" value="Save changes"/>
        </fieldset>
    </form>
</div>