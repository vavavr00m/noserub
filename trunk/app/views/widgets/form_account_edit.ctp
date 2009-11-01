<?php 
    $services = Configure::read('services.data');
    $service_types = Configure::read('service_types_list'); 
    unset($service_types[0]);
?>
<div class="widget form-account-edit">
    <h2><?php __('Edit account'); ?></h2>
    <?php 
    echo $form->create(array('url' => '/settings/accounts/edit/id:' . $this->data['Account']['id']));
    echo $noserub->fnSecurityTokenInput();
    echo $form->input('Account.id', array('type' => 'hidden'));
    echo __('Service', true) . ': ' . $services[$this->data['Account']['service']]['name'];
    if(!$services[$this->data['Account']['service']]['is_contact']) {
        echo $form->input('Account.service_type', array('label' => __('Service Type', true), 'type' => 'select', 'options' => $service_types));
    }
    if($this->data['Account']['service'] != 'RSS-Feed') {
        echo $form->input('Account.username', array('label' => __('Username', true)));
    }
    echo $form->input('Account.title', array('label' => __('Label', true) . ' (' . __('optional', true) . ')'));
    echo $form->input('Account.account_url', array('label' => __('Account URL', true)));
    if(!$services[$this->data['Account']['service']]['is_contact']) {
        echo $form->input('Account.feed_url', array('label' => __('Feed URL', true) ));
    }
    echo $form->end(array('label' => __('Save', true))); 
    ?>
    <h2><?php __('Delete account'); ?></h2>
    <p>
        <?php __('Delete this account, all entries and comments associated with it.'); ?>
    </p>
    <?php 
    echo $form->create(array('url' => '/settings/accounts/delete/'));
    echo $noserub->fnSecurityTokenInput();
    echo $form->input('Account.id', array('type' => 'hidden'));
    echo $form->end(array('label' => __('Delete account', true))); 
    ?>
</div>