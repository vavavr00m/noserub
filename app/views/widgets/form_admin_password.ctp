<div class="widget form-admin-password">
    <?php if($this->data) {
        echo $form->create(array('url' => '/admins/password/'));

        echo '<p>';
        __('If you want to change the admins password, enter the current one and two times the new one.');
        echo '</p>';
        echo '<fieldset><legend>' . __('Admin Password', true) . '</legend>';
        echo $form->input('Admin.password', array('type' => 'password', 'label' => __('Current password', true)));
        echo $form->input('Admin.new_password', array('type' => 'password', 'label' => __('New password', true)));
        echo $form->input('Admin.new_password2', array('type' => 'password', 'label' => __('New password (repeated)', true)));
        echo '</fieldset>';
    
        echo $form->submit(__('Save', true));
        echo $form->submit(__('Cancel', true), array('name' => 'cancel'));
        echo $form->end(null);
    } else { ?>
        <p>
            <?php __('You need to be logged in to the Admin area to see something here.'); ?>
        </p>
    <?php } ?>
</div>