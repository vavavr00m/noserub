<form id="AccountAddFormStep2" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <?php echo $form->input('Account.username', array('label' => 'Username in '.$service['Service']['name'].':', 
                                                          'error' => 'Could not retrieve any information for this username.')); ?>        
        <?php echo $form->submit('Peview >'); ?>
    </fieldset>
<?php echo $form->end(); ?>