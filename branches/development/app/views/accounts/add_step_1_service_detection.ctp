<form id="AccountAddFormStep1" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
    	<legend>Specify the service url</legend>
    	<?php echo $form->input('Account.url', array('error' => 'Could not detect a service or a feed')); ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Next step"/>
    </fieldset>
<?php echo $form->end(); ?>