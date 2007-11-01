<form id="AccountAddFormStep1" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
    	<legend>Add service</legend>
    	<input type="radio" name="data[Account][type]" id="type_1" id="AccountType" value="1" checked="checked" />
        <?php
            echo 'Service: ' . $form->select('Account.service_id', $services, null, null, false); 
        ?>
        <br />
        <input type="radio" name="data[Account][type]" id="type_2" id="AccountType" value="2" />
        Any Feed (RSS or Atom, eg. from Blogs)
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Next step"/>
    </fieldset>
<?php echo $form->end(); ?>