<?php if(!$data || empty($data['Account'])) { ?>
    <h1>No Information available</h1>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
    <?php echo $this->renderElement('foaf'); ?>

    <?php if(!empty($data['Contact'])) { ?>
        <div style="float:left;margin:10px;">
            <h1>Contacts</h1>
            <?php echo $this->renderElement('contacts/index', array('data' => $data['Contact'])); ?>
        </div>
    <?php } ?>
    
    <div style="float:left;margin:10px;">
        <h1>Accounts</h1>
        <?php echo $this->renderElement('accounts/index', array('data' => $data['Account'])); ?>
        <?php if($session_identity_id == $data['Identity']['id']) { ?>
            <?php echo $html->link('Add new account', '/' . $data['Identity']['username'] . '/accounts/add/'); ?>
        <?php } ?>
    </div>

    <div style="clear:both;margin:10px;">
        <h1>Items</h1>
        <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
    </div>
<?php } ?>