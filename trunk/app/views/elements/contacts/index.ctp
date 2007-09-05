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
                            <?php echo $html->link($item['WithIdentity']['local'] == 1 ? $item['WithIdentity']['username'] : $item['WithIdentity']['full_username'], 
                                                   $item['WithIdentity']['url']); ?>
                        </td>
                        <td><?php echo $item['WithIdentity']['namespace'] == $session->read('Identity.username') ? 'Local' : 'NoseRub'; ?></td>
                        <td>
                            <?php echo $html->link('Remove Contact', '/' . $session->read('Identity.username') . '/contacts/' . (isset($item['Contact']['id']) ? $item['Contact']['id'] : $item['id']) . '/delete'); ?>
                            <?php if($item['WithIdentity']['namespace'] == $session->read('Identity.username')) { ?>
                                | <?php echo $html->link('Add Account', '/' . $item['WithIdentity']['full_username'] . '/accounts/add/'); ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
<?php } ?>
<?php if($identity['id'] == $session->read('Identity.id')) {
    echo $html->link('Add new contact', '/' . $identity['full_username'] . '/contacts/add/'); 
} ?>