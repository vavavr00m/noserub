<form id="AccountAddFormStep1" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
    	<legend>Specify an url and let us try to auto discover</legend>
    	<?php echo $form->input('Account.url', array('error' => 'Could not detect a service or a feed', 'size' => 64)); ?>
    </fieldset>
    <fieldset>
    <p>- OR -</p>
    </fieldset>
    <fieldset>
    	<legend>Specify service and account id</legend>
    	<label>Service</label>
    	<?php echo $form->select('Account.service_id', $services, null, null, false); ?><br />
    	<?php echo $form->input('Account.username', array('error' => 'Could not detect a service or a feed', 'label' => 'Account id / Username', 'size' => 64)); ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Next step"/>
    </fieldset>
<?php echo $form->end(); ?>