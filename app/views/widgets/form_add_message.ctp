<div class="widget form-add-message">
    <?php if(Context::isLoggedIn()) { ?>
        <?php 
        echo $form->create(array('url' => '/messages/add/'));
        echo $noserub->fnSecurityTokenInput();
        echo $form->input('Message.to_from', array('label' => __('To', true)));
        echo $form->input('Message.subject', array('label' => __('Subject', true)));
        echo $form->input('Message.text', array('type' => 'textarea', 'label' => false, 'escape' => false));

        echo $form->end(array('label' => __('Send', true))); 
    } ?> 
</div>