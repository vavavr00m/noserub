<h1>Add new Account</h1>
<?php if(isset($with_identity)) { ?>
    <p>
        Your're about to add a new account for your contact <strong><?php $with_identity['Identity']['username']; ?></strong>
    </p>
<?php } else { ?>
    <p>
        This is for adding all your accounts to your profile. If you want to add someone elses account, create a contact first!
    </p>
<?php } ?>
<form id="AccountAddForm" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <?php 
            echo $form->select('Account.service_id', $services, null, null, false); 
        ?>
        <?php 
            echo $form->input('Account.username', 
                              array('error' => array(
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes and dots',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes and dots only'))); 
        ?>
        <?php echo $form->submit('Add'); ?>
    </fieldset>
<?php echo $form->end(); ?>