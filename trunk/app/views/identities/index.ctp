<?php if(!$data) { ?>
    <h1>No Information available</h1>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
    <?php $openid->serverLink('/auth', false); ?>
    
    <h1><?php echo $data['Identity']['username']; ?>'s NoseRub page</h1>
    
    <?php echo $this->renderElement('foaf'); ?>

    <div style="float:left;margin:10px;">
        <h1>Contacts</h1>
        <?php echo $this->renderElement('contacts/index', array('data' => $data['Contact'], 'identity' => $data['Identity'])); ?>
    </div>
    
    <div style="float:left;margin:10px;">
        <h1>Accounts</h1>
        <?php echo $this->renderElement('accounts/index', array('data' => $data['Account'], 'identity' => $data['Identity'])); ?>
    </div>

    <div style="clear:both;margin:10px;">
        <h1>Items</h1>
        <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
    </div>
<?php } ?>