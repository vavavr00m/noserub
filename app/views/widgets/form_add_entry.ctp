<h2><?php __('Add something new'); ?></h2>
<?php 
echo $form->create(array('url' => '/entry/add/'));
echo $noserub->fnSecurityTokenInput();
echo $form->input('service_type', array('value' => 'micropublish', 'type' => 'hidden'));
echo $form->input('text', array('type' => 'textarea', 'label' => __('Micropublish', true)));
echo $form->end(array('label' => __('Send', true))); 
?>