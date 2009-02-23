<?php if($context['logged_in_identity']) { ?>
    <?php if(!$context['admin_id']) { ?>
        <h2><?php __('Admin-Login'); ?></h2>
        <?php echo $form->create(array('url' => '/admins/login/')); ?>
        <?php echo $form->input('Admin.username', array('label' => __('Admin username', true))); ?>
        <?php echo $form->input('Admin.password', array('type' => 'password')); ?>
        <?php echo $form->end(array('label' => __('Login', true))); ?>
    <?php } ?>
<?php } else { ?>
    <p>
        <?php __('You need to be logged in to this network in order to be able to access the admin login.'); ?>
    </p>
<?php } ?>