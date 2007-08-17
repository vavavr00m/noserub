<?php if(!$data || empty($data['Account'])) { ?>
    <h1>No Information available</h1>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
    <?php echo $this->renderElement('foaf'); ?>
    <h1>Accounts</h1>
    <?php echo $this->renderElement('accounts/index', array('data' => $data['Account'])); ?>
    <?php if($session_identity_id == $data['Identity']['id']) { ?>
        <?php echo $html->link('Add new account', '/' . $data['Identity']['username'] . '/accounts/add/'); ?>
    <?php } ?>
    
    <h1>Contacts</h1>
    <?php echo $this->renderElement('contacts/index', array('data' => $data['Contact'])); ?>
<?php } ?>