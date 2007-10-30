<form id="AccountAddFormStep2" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
    	<legend>Details</legend>
        <?php echo $form->input('Account.feed_url', array('label' => 'RSS-Feed:', 
                                                          'size'  => 64,
                                                          'error' => 'The RSS-Feed could not be read.')); ?>
        <label>
        <?php echo $form->select('Account.service_type_id', $service_types, array('3'), null, null, false); ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Preview >"/>           
    </fieldset>
<?php echo $form->end(); ?>