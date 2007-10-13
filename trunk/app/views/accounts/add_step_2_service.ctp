<form id="AccountAddFormStep2" method="post" action="<?php echo $this->here ?>">
    <fieldset>
    	<legend>Details</legend>
        <?php echo $form->input('Account.username', array('label' => 'Username in '.$service['Service']['name'].':', 
                                                          'error' => 'Could not retrieve any information for this username.')); ?>        
     </fieldset>
     <?php if($service['Service']['help']) { ?>
        <fieldset>
            <p>
                <?php echo $service['Service']['help']; ?>
            </p>
        </fieldset>
     <?php } ?>
     <fieldset>
        <input class="submitbutton" type="submit" value="Preview >"/>
    </fieldset>
<?php echo $form->end(); ?>