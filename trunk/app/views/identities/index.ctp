<?php if(!$data) { ?>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
    <?php 
        $noserub_url = 'http://' . $data['Identity']['username'];
    ?>
	<?php $openid->xrdsLocation($noserub_url . '/xrds', false); ?>
	<?php $openid->serverLink('/auth', false); ?>
    
    <?php echo $this->renderElement('foaf'); ?>

    <p>
        <a href="<?php echo $noserub_url; ?>"><?php echo $data['Identity']['local_username']; ?></a> has <a href="<?php echo $noserub_url . '/contacts/'; ?>"><strong>
        <?php echo $num_noserub_contacts; ?></strong> NoseRub contacts</a>, <strong><?php echo $num_private_contacts; ?></strong> private contacts and added
        <a href="<?php echo $noserub_url . '/accounts/'; ?>"><strong><?php echo $num_accounts; ?></strong> accounts</a>
        to <?php switch($data['Identity']['sex']) {
                case  1: echo 'her'; break;
                case  2: echo 'his'; break;
                default: echo 'her/his'; break;
            }?> profile.
    </p>
    <?php if(isset($distance)) { ?>
        <p>
            <?php echo $data['Identity']['local_username']; ?> lives <?php echo ceil($distance); ?> km away from you.
        </p>
    <?php } ?>
    
    <br class="clear" />

    <div>
        <h2>Social activity</h2>
        <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
    </div>
<?php } ?>