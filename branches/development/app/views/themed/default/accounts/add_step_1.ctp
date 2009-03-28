<div id="inhalt">
    <form id="AccountAddFormStep1" method="post" action="<?php echo $this->here ?>">
        <input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
        <fieldset>
            <legend><?php __('Specify an url and let us try to auto discover'); ?></legend>
            <?php echo $form->input('Account.url', array('error' => __('Could not detect a service or a feed', true), 'size' => 64)); ?>
        </fieldset>
        <fieldset>
        <p>- <?php __('OR'); ?>-</p>
        </fieldset>
        <fieldset>
            <legend><?php __('Specify service and account id'); ?></legend>
            <label><?php __('Service'); ?></label>
            <?php echo $form->select('Account.service_id', $services, null, array(), false); ?><br />
            <?php echo $form->input('Account.username', array('error' => __('Could not detect a service or a feed', true), 'label' => 'Account id / Username', 'size' => 64)); ?>
        </fieldset>
        <fieldset>
            <input class="submitbutton" type="submit" value="<?php __('Next step'); ?>"/>
        </fieldset>
    <?php echo $form->end(); ?>
</div>

<div id="rechts">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>