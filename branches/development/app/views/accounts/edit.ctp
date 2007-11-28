<form id="AccountEdit" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
    	<legend>Edit service details</legend>
        <?php echo $form->input('Account.title'); ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Change"/>
    </fieldset>
<?php echo $form->end(); ?>
