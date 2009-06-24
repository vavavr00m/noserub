<?php 
echo $form->create(array('url' => '/groups/add/'));
echo $noserub->fnSecurityTokenInput();
echo $form->input('Group.name', array('label' => __('Name of the group', true)));
echo $form->input('Group.description', array('label' => __('Description', true)));
echo $form->end(array('label' => __('Create group', true)));