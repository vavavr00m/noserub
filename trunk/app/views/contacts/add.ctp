<h1>Add new Contact</h1>
<p>
    You can either enter a valid NoseRub-ID (<em>http://noserubserver.com/my.buddy</em>)
    or just a new username, that then will created for your access only.
</p>
<p>
    Whenever you have a NoseRub-ID from someone, you should use that to add
    a new contact.
</p>
<form id="ContactAddForm" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <legend>Add a contact with a NoseRub-ID</legend>
        <?php 
            echo $form->input('Contact.username', 
                              array('error' => array(
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes, dots and "@"',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes, dots and "@" only',
                                    'unique'   => 'The username is alreay taken'))); 
        ?>
        <?php echo $form->submit('Add'); ?>
    </fieldset>
    <br />
    <strong>OR</strong>
    <br />
    <fieldset>
        <legend>Create a local contact</legend>
        <?php 
            echo $form->input('Contact.username', 
                              array('error' => array(
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes, dots and "@"',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes, dots and "@" only',
                                    'unique'   => 'The username is alreay taken'))); 
        ?>
        <?php echo $form->submit('Create'); ?>
    </fieldset>
<?php echo $form->end(); ?>