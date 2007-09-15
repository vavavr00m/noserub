<form id="AccountAddFormStep2" method="post" action="<?php echo $this->here ?>">
    <fieldset>
    	<legend>Details</legend>
        <?php echo $form->input('Account.username', array('label' => 'Username in '.$service['Service']['name'].':', 
                                                          'error' => 'Could not retrieve any information for this username.')); ?>        
     </fieldset>
     <fieldset>
        <input class="submitbutton" type="submit" value="Preview >"/>
    </fieldset>
<?php echo $form->end(); ?>