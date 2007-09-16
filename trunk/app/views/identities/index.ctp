<?php if(!$data) { ?>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
	<?php $openid->xrdsLocation('http://'.$data['Identity']['username'].'/xrds', false); ?>
	<?php $openid->serverLink('/auth', false); ?>
    
    <?php echo $this->renderElement('foaf'); ?>
    
    <?php if(isset($distance)) { ?>
        <p>
            <?php echo $data['Identity']['local_username']; ?> lives <?php echo ceil($distance); ?> km from you.
        </p>
    <?php } ?>
    <?php if(isset($filter) && ($filter == false || $filter == 'all')) { ?>
        <div style="float:left;margin:10px;">
            <h1>Contacts</h1>
            <?php echo $this->renderElement('contacts/index', array('data' => $data['Contact'], 'identity' => $data['Identity'])); ?>
        </div>
    
        <div style="float:left;margin:10px;">
            <h1>Accounts</h1>
            <?php echo $this->renderElement('accounts/index', array('data' => $data['Account'], 'identity' => $data['Identity'])); ?>
        </div>
    <?php } ?>
    
    <div style="clear:both;margin:10px;">
        <h1>Items</h1>
        <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
    </div>
<?php } ?>