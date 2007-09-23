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
            <?php echo $data['Identity']['local_username']; ?> lives <?php echo ceil($distance); ?> km away from you.
        </p>
    <?php } ?>
    <?php if(isset($filter) && ($filter === false || $filter === 'all')) { ?>
        <div class="yourboxes">
            <h2>Your Contacts</h2>
            <?php echo $this->renderElement('contacts/index', array('data' => $data['Contact'], 'identity' => $data['Identity'])); ?>
        </div>
    
        <div class="yourboxes">
            <h2>Your Accounts</h2>
            <?php echo $this->renderElement('accounts/index', array('data' => $data['Account'], 'identity' => $data['Identity'])); ?>
        </div>
    <?php } ?>
    
    <br class="clear" />

    <div>
        <h2>Your social activity</h2>
        <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
    </div>
<?php } ?>