<div class="widget widget-admin-login">
    <?php if(!Context::isLoggedInIdentity() && !Context::read('show_admin_login')) { ?>
        <h2><?php __('Admin-Login'); ?></h2>
        <p>
            <?php __('Please log in with your network username before accessing the admin login.'); ?>
        </p>
    <?php } else if(!Context::isAdmin()) { ?>
        <h2><?php __('Admin-Login'); ?></h2>
        <?php echo $form->create(array('url' => '/admins/login/')); ?>
        <?php echo $form->input('Admin.username', array('label' => __('Admin username', true))); ?>
        <?php echo $form->input('Admin.password', array('type' => 'password')); ?>
        <?php echo $form->end(array('label' => __('Login', true))); ?>
    <?php } ?>
</div>