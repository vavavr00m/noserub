<?php
    $session_local_username = isset($session_identity['local_username']) ? $session_identity['local_username'] : '';
    $session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;
?>

<?php if(empty($data)) { ?>
        <p>
            No contacts yet.
        </p>
<?php } else { ?>
    <table class="listing">
        <thead>
            <tr>
                <th>Username</th>
                <th>Type</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($data)) {
                foreach($data as $item) { ?>
                    <tr>
                        <td>
                            <?php echo $html->link($item['WithIdentity']['local_username'], 'http://' . $item['WithIdentity']['username']); ?>
                        </td>
                        <td><?php echo $session_local_username ==  $item['WithIdentity']['namespace'] ? 'Local' : 'NoseRub'; ?></td>
                        <td>
                            <?php echo $html->link('Remove Contact', '/' . $session_local_username . '/contacts/' . (isset($item['Contact']['id']) ? $item['Contact']['id'] : $item['id']) . '/delete'); ?>
                            <?php if($item['WithIdentity']['namespace'] == $session_local_username) { ?>
                                | <?php echo $html->link('Add Account', '/' . $item['WithIdentity']['local_username'] . '/accounts/add/'); ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
<?php } ?>
<?php if($identity['id'] == $session_identity_id) {
    echo $html->link('Add new contact', '/' . $identity['local_username'] . '/contacts/add/'); 
} ?>