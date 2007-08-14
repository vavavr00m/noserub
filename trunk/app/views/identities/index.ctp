<?php if(!$data || empty($data['Account'])) { ?>
    <h1>No Information available</h1>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
<?php echo $this->renderElement('foaf'); ?>
    <h1>Accounts</h1>
    <table class="listing">
        <tr>
            <th>Account</th>
            <th>RSS-Feed</th>
            <th>Service</th>
        </tr>
        <?php foreach($data['Account'] as $account) { ?>
            <tr>
                <td><?php echo $account['username']; ?></td>
                <td><?php echo $account['feed_url']; ?></td>
                <td><?php echo $html->link($account['Service']['name'], $account['account_url']); ?></a></td>
            </tr>
        <?php } ?>
    </table>
    <?php if($session_identity_id == $data['Identity']['id']) { ?>
        <?php echo $html->link('Add new account', '/' . $data['Identity']['username'] . '/accounts/add/'); ?>
    <?php } ?>

    <h1>Contacts</h1>
    <table class="listing">
        <tr>
            <th>Name</th>
        </tr>
        <?php foreach($data['Contact'] as $contact) { 
            if($contact['WithIdentity']['namespace'] != '' && $contact['identity_id'] != $session_identity_id) {
                # do not display contacts that are only locally available
                continue;
            } ?>
            <tr>
                <td>
                    <?php echo $html->link($contact['WithIdentity']['domain'] == NOSERUB_DOMAIN ? $contact['WithIdentity']['username'] : $contact['WithIdentity']['full_username'],
                                           $contact['WithIdentity']['url']); ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>