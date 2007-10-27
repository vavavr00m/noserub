<form id="AccountAddFormStep1" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
    	<legend>Add service</legend>
        <?php
            $selects = 'Service: ' . $form->select('Account.service_id', $services, null, null, false); 
            echo $form->radio('Account.type', array(1 => '', 2 => ''), array('label' => false, 'separator' => $selects.'<br />'));
        ?>
        Any RSS-Feed (eg. Blogs)
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Next step"/>
    </fieldset>
<?php echo $form->end(); ?>