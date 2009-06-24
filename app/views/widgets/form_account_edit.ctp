<h2><?php __('Edit account'); ?></h2>
<?php 
echo $form->create(array('url' => '/settings/accounts/edit/id:' . $this->data['Account']['id']));
echo $noserub->fnSecurityTokenInput();
echo $form->input('id', array('type' => 'hidden'));
echo __('Service', true) . ': ' . $services[$this->data['Account']['service_id']];
if(!$this->data['Service']['is_contact']) {
    echo $form->input('service_type_id', array('label' => __('Service Type', true), 'type' => 'select', 'options' => $service_types));
}
echo $form->input('username', array('label' => __('Username', true)));
echo $form->input('title', array('label' => __('Label', true) . ' (' . __('optional', true) . ')'));
echo $form->input('account_url', array('label' => __('Account URL', true)));
if(!$this->data['Service']['is_contact']) {
    echo $form->input('feed_url', array('label' => __('Feed URL', true) ));
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
echo $form->input('id', array('type' => 'hidden'));
echo $form->end(array('label' => __('Delete account', true))); 
?>