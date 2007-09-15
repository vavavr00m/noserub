<form id="AccountAddFormStep1" method="post" action="<?php echo $this->here ?>">
    <fieldset>
    	<legend>Add service</legend>
        <?php
            $services = 'Service: ' . $form->select('Account.service_id', $services, null, null, false); 
            echo $form->radio('Account.type', array(1 => '', 2 => ''), $services.'<br />');
        ?>
        Any RSS-Feed (eg. Blogs)
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Next step"/>
    </fieldset>
<?php echo $form->end(); ?>