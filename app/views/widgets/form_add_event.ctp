<div class="widget form-add-event">
    <?php if(Context::isLoggedIn()) { ?>
        <?php 
        echo $form->create(array('url' => '/events/add/'));
        echo $noserub->fnSecurityTokenInput();
        echo $form->input('Event.name', array('label' => __('Name', true)));
        echo $form->input('Event.location_id', array('label' => __('Location', true), 'type' => 'select', 'options' => $locations, 'value' => 0));
        echo $form->input('Event.type', array('label' => __('Type', true), 'type' => 'select', 'options' => $types));
        echo $form->input('Event.from_datetime', array('timeFormat' => '24', 'label' => __('From', true)));
        echo $form->input('Event.to_datetime', array('timeFormat' => '24', 'label' => __('From', true)));
        echo $form->input('Event.all_day', array('type' => 'checkbox', 'label' => __('All day', true)));
        echo $form->input('Event.description', array('type' => 'textarea', 'label' => false, 'escape' => false));
        echo $form->input('Event.url', array('label' => __('URL', true)));
        echo $form->end(array('label' => __('Create', true))); 
    } ?> 
</div>