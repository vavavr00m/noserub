<h1>Your contacts</h1>
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
                        <?php echo $html->link($item['WithIdentity']['domain'] == NOSERUB_DOMAIN ? $item['WithIdentity']['username'] : $item['WithIdentity']['full_username'], 
                                               $item['WithIdentity']['url']); ?>
                    </td>
                    <td><?php echo $item['WithIdentity']['namespace'] == $session->read('Identity.username') ? 'Local' : 'NoseRub'; ?></td>
                    <td>
                        <?php echo $html->link('Remove Contact', '/' . $session->read('Identity.username') . '/contacts/' . $item['Contact']['id'] . '/delete'); ?>
                        <?php if($item['WithIdentity']['namespace'] == $session->read('Identity.username')) { ?>
                            | <?php echo $html->link('Add Account', '/' . $session->read('Identity.username') . '/contacts/' . $item['WithIdentity']['id'] . '/accounts/add/'); ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php }
        } ?>
    </tbody>
</table>
<?php echo $html->link('Add new contact', '/' . $session->read('Identity.username') . '/contacts/add/'); ?>
