<h2><?php __('Edit Location'); ?></h2>
<p>
    <?php __('The name will be displayed, when you choose a location as your curent one. <br />
    The address is only used to geocode the location and will not be displayed.<br />
    You can mostly just use <em>Town, Country</em> to specify an address.'); ?>
</p>
<?php
echo $form->create(array('url' => '/settings/locations/edit/'));
echo $noserub->fnSecurityTokenInput();
echo $form->input('id', array('type' => 'hidden'));
echo $form->input('name', array('label' => __('Name', true), 'size' => 64));
echo $form->input('address', array('label' => __('Address', true), 'size' => 64));
echo $form->end(array('label' => __('Save', true))); 
?>
<h2><?php __('Delete Location'); ?></h2>
<?php 
echo $form->create(array('url' => '/settings/locations/delete/'));
echo $noserub->fnSecurityTokenInput();
echo $form->input('id', array('type' => 'hidden'));
echo $form->end(array('label' => __('Delete location', true))); 
?>