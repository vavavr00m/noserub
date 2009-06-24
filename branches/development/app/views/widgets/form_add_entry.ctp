<h2><?php __('Add something new'); ?></h2>
<?php 
echo $form->create(array('url' => '/entries/add/'));
echo $noserub->fnSecurityTokenInput();
echo $form->input('service_type', array('value' => 'micropublish', 'type' => 'hiddden'));
echo $form->input('micropublish', array('type' => 'textarea', 'label' => __('Micropublish', true)));
echo $form->end(array('label' => __('Send', true))); 
?>