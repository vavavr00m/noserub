<?php if(Context::isLoggedIn() && Context::entryId()) { ?>
    <h2><?php __('Leave your comment'); ?></h2>
    <?php 

    echo $form->create(array('url' => '/comments/add/'));
    echo $noserub->fnSecurityTokenInput();
    echo $form->input('Comment.entry_id', array('value' => Context::entryId(), 'type' => 'hidden'));
    echo $form->input('Comment.text', array('type' => 'textarea', 'label' => false));

    echo $form->end(array('label' => __('Send', true))); 
} ?> 
