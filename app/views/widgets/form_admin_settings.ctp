<?php 

if($this->data) {
    echo $form->create(array('url' => '/admins/settings/'));

    echo '<fieldset><legend>' . __('Network', true) . '</legend>';
    echo $form->input('Network.url');
    echo $form->input('Network.name');
    echo $form->input('Network.description');
    echo $form->label('Network.default_language', __('Default language', true));
    echo $form->select('Network.default_language', Configure::read('Languages'), null, null, false);
    echo '</fieldset>';
    
    echo '<fieldset><legend>' . __('Location', true) . '</legend>';
    echo $form->input('Network.latitude');
    echo $form->input('Network.longitude');
    echo $form->input('Network.google_maps_key');
    echo '</fieldset>';
    
    echo '<fieldset><legend>' . __('Registration', true) . '</legend>';
    $registration_types = array(
        1 => __('Everyone may register', true),
        2 => __('No one may register', true)
        #3 => __('By invitation only', true)
    );
    echo $form->label('Network.registration_type', __('Type of registration policy', true));
    echo $form->select('Network.registration_type', $registration_types, null, null, false);
    echo $form->input('Network.registration_restricted_hosts');
    echo '</fieldset>';

    echo '<fieldset><legend>' . __('Misc', true) . '</legend>';
    echo $form->input('Network.use_ssl');    
    echo $form->input('Network.api_info_active');
    echo $form->input('Network.allow_subscriptions');
    echo '</fieldset>';
    
    echo $form->submit(__('Save', true));
    echo $form->submit(__('Cancel', true), array('name' => 'cancel'));
    echo $form->end(null);
} else { ?>
    <p>
        <?php __('You need to be logged in to the Admin area to see something here.'); ?>
    </p>
<?php }