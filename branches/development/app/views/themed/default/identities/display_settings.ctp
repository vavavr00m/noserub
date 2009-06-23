<div id="bd-main-hd">
</div>
<div id="bd-main-bd">
    <?php echo $noserub->widgetFlashMessage(); ?>
    <form id="IdentityDisplaySettingsForm" method="post" action="<?php echo $this->here; ?>">
        <input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
        <fieldset>
            <legend>
                <?php __('Filter, which should be shown, when selecting <em>Overview</em> as filter.'); ?>
            </legend>
            <ul>
            <?php foreach($filters as $value => $label) { ?>
                <li><input 
                    type="checkbox" 
                    <?php if(in_array($value, $this->data['Identity']['overview_filters'])) { ?>
                        checked="checked"
                    <?php } ?>
                    name="data[Identity][overview_filters][]" 
                    value="<?php echo $value; ?>">
                <?php echo $label; ?>
                </li>
            <?php } ?>
            </ul>
        </fieldset>
        <fieldset>
            <input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
        </fieldset>
    </form>
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>