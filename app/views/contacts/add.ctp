<h1>Add new Contact</h1>
<p>
    After you created a contact, you can add services to him/her.
</p>
<form id="ContactAddForm" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <?php 
            echo $form->input('Contact.username', 
                              array('error' => array(
                                    'required' => 'You need to enter something here. Valid characters: letters ,numbers, underscores, dashes and dots',
                                    'content'  => 'Valid characters: letters, numbers, underscores, dashes and dots only',
                                    'unique'   => 'The username is alreay taken'))); 
        ?>
        <?php echo $form->submit('Add'); ?>
    </fieldset>
<?php echo $form->end(); ?>