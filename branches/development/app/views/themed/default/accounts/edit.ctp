<div id="inhalt">
    <form id="AccountEdit" method="post" action="<?php echo $this->here ?>">
        <input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
        <fieldset>
        	<legend><?php __('Edit service details'); ?></legend>
            <?php echo $form->input('Account.title'); ?>
            <label for="AccountServiceTypeId"><?php __('Servicetype'); ?></label>
            <?php echo $form->select('Account.service_type_id', $service_types, array($this->data['Account']['service_type_id']), array(), false); ?>
        </fieldset>
        <fieldset>
            <input class="submitbutton" type="submit" value="<?php __('Change'); ?>"/>
        </fieldset>
    <?php echo $form->end(); ?>
</div>

<div id="rechts">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>