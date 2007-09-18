<p>
    You can either enter a valid NoseRub-ID (<em>noserubserver.com/my.buddy</em>)
    or just a new username, that then will created for your access only (a private contact).
</p>
<p>
    Whenever you have a NoseRub-ID from someone, you should use that to add
    a new contact.
</p>
<form id="ContactAddForm" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <legend>Add a contact with a NoseRub-ID</legend>
        <?php 
            echo $form->input('Contact.noserub_id', 
                              array('label' => 'NoseRub-ID',
                                    'size'  => 32,
                                    'error' => array(
                                        'user_not_found'      => 'This user could not be found at the other server',
                                        'no_valid_noserub_id' => 'This is no valid NoseRub-ID. It must contain at least one "/"!'))); 
        ?>
        <input type="submit" value="Add" name="add" class="submitbutton">
    </fieldset>
    
    <p>
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