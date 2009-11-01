<div class="widget form-add-comment">
    <?php if(Context::isLoggedIn() && Context::entryId() &&
        (!Context::groupId() || (Context::groupId() && Context::isSubscribed()))) { ?>
        <h2><?php __('Leave your comment'); ?></h2>
        <?php 

        echo $form->create(array('url' => '/comments/add/'));
        echo $noserub->fnSecurityTokenInput();
        echo $form->input('Comment.entry_id', array('value' => Context::entryId(), 'type' => 'hidden'));
        echo $form->input('Comment.text', array('type' => 'textarea', 'label' => false, 'class' => 'wysiwyg'));

        echo $form->end(array('label' => __('Send', true))); 
    } ?> 
</div>