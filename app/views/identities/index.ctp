<?php if(!$data || empty($data['Account'])) { ?>
    <h1>No Information available</h1>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
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
                <td><?php echo $account['feedurl']; ?></td>
                <td><?php echo $account['Service']['name']; ?></td>
            </tr>
        <?php } ?>
    </table>
    <?php if($session_identity_id == $data['Identity']['id']) { ?>
        <a href="http://noserub/noserub/<?php echo $data['Identity']['username']; ?>/accounts/add/">Add new account</a>
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
                <td><a href="<?php echo $contact['WithIdentity']['url']; ?>"><?php echo $contact['WithIdentity']['full_username']; ?></a></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>