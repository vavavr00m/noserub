<?php $flashmessage->render(); ?>
<form id="IdentityDisplaySettingsForm" method="post" action="<?php echo $this->here; ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
        <legend>
            Filter, which should be shown, when selecting <em>Overview</em> as filter.
        </legend>
        <?php foreach($filters as $value => $label) { ?>
            <input 
                type="checkbox" 
                <?php if(in_array($value, $this->data['Identity']['overview_filters'])) { ?>
                    checked="checked"
                <?php } ?>
                name="data[Identity][overview_filters][]" 
                value="<?php echo $value; ?>">
            <?php echo $label; ?><br />
        <?php } ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Save changes"/>
    </fieldset>
</form>