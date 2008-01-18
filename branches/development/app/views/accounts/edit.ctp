<form id="AccountEdit" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
    	<legend>Edit service details</legend>
        <?php echo $form->input('Account.title'); ?>
        <label for="AccountServiceTypeId">Servicetype</label>
        <?php echo $form->select('Account.service_type_id', $service_types, array($this->data['Account']['service_type_id']), null, null, false); ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Change"/>
    </fieldset>
<?php echo $form->end(); ?>
