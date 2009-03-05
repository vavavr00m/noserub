<?php if(!Configure::read('context.admin_id')) { ?>
    <h2><?php __('Admin-Login'); ?></h2>
    <?php echo $form->create(array('url' => '/admins/login/')); ?>
    <?php echo $form->input('Admin.username', array('label' => __('Admin username', true))); ?>
    <?php echo $form->input('Admin.password', array('type' => 'password')); ?>
    <?php echo $form->end(array('label' => __('Login', true))); ?>
<?php } ?>