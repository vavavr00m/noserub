<div class="widget form-add-location">
    <?php if(Context::isLoggedIn()) { ?>
        <?php 
        echo $form->create(array('url' => '/locations/add/'));
        echo $noserub->fnSecurityTokenInput();
        echo $form->input('Location.name', array('label' => __('Name', true)));
        echo $form->input('Location.address', array('label' => __('Address', true)));
        echo $form->input('Location.type', array('label' => __('Type', true), 'type' => 'select', 'options' => $types));
        echo $form->input('Location.description', array('type' => 'textarea', 'label' => false, 'escape' => false));
        echo $form->input('Location.url', array('label' => __('URL', true)));
        echo $form->end(array('label' => __('Create', true))); 
    } ?> 
</div>