<?php echo $form->create(array('url' => '/groups/add/')); ?>
<input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
<?php echo $form->input('Group.name', array('label' => __('Name of the group', true))); ?>
<?php echo $form->input('Group.description', array('label' => __('Description', true))); ?>
<?php echo $form->end(array('label' => __('Create group', true))); ?>