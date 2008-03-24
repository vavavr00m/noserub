<p class="infotext">
    You can either enter an URL or a NoseRub-ID (<em>noserubserver.com/MyBuddy</em>)
    or just a new username, that then will created a private contact which is for your access only!
</p>
<p class="infotext">
    Whenever you have an URL of someone, you should try that to add a new contact.
</p>
<form id="ContactAddForm" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <legend>Add a contact with an URL or NoseRub-ID</legend>
        <?php 
            echo $form->input('Contact.noserub_id', 
                              array('label' => 'NoseRub-ID / URL',
                                    'size'  => 32,
                                    'error' => array(
                                        'user_not_found'      => 'This user could not be found at the other server',
                                        'no_valid_noserub_id' => 'This is no valid NoseRub-ID or URL!',
                                        'own_noserub_id'      => 'You cannot add yourself as a contact.',
                              			'unique'              => 'This user has already been added as a contact.'))); 
        ?>
        <input type="submit" value="Add" name="add" class="submitbutton">
    </fieldset>
    
    <p class="infotext">
    	<strong>OR</strong>
    <p>
    
    <fieldset>
        <legend>Create a private contact</legend>
        <?php 
            echo $form->input('Contact.username', 
                              array('error' => array(
                                    'size'     => 32,
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes, dots and "@"',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes, dots and "@" only',
                                    'unique'   => 'The username is alreay taken'))); 
        ?>
        <input type="submit" value="Create" name="create" class="submitbutton">
    </fieldset>
<?php echo $form->end(); ?>