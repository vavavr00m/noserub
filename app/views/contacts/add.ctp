<p class="infotext">
    <?php __('You can either enter an URL or a NoseRub-ID (<em>noserubserver.com/MyBuddy</em>) or just a new username, that then will created a private contact which is for your access only!'); ?>
</p>
<p class="infotext">
    <?php __("Whenever you have an URL of someone, you should try that to add a new contact. Naturally you would want to add a contact's blog as an URL, or his/her FriendFeed-URL."); ?>
</p>
<form id="ContactAddForm" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <legend><?php __('Add a contact with an URL or NoseRub-ID'); ?></legend>
        <?php 
            echo $form->input('Contact.noserub_id', 
                              array('label' => __('NoseRub-ID / URL', true),
                                    'size'  => 32,
                                    'error' => array(
                                        'user_not_found'      => __('This user could not be found at the other server', true),
                                        'no_valid_noserub_id' => __('This is no valid NoseRub-ID or URL!', true),
                                        'own_noserub_id'      => __('You cannot add yourself as a contact.', true),
                              			'unique'              => __('This user has already been added as a contact.', true)))); 
        ?>
        <input type="submit" value="<?php __('Add'); ?>" name="add" class="submitbutton">
    </fieldset>
    
    <p class="infotext">
    	<strong><?php __('OR'); ?></strong>
    <p>
    
    <fieldset>
        <legend><?php __('Create a private contact'); ?></legend>
        <?php 
            echo $form->input('Contact.username', 
                              array('error' => array(
                                    'size'     => 32,
                                    'required' => __('You need to enter something here. Valid characters: letters ,numbers, underscores, dashes, dots and "@"', true),
                                    'content'  => __('Valid characters: letters, numbers, underscores, dashes, dots and "@" only', true),
                                    'unique'   => __('The username is alreay taken', true)))); 
        ?>
        <input type="submit" value="<?php __('Create'); ?>" name="create" class="submitbutton">
    </fieldset>
<?php echo $form->end(); ?>