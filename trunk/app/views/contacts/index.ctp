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
                        <a href="<?php echo $item['WithIdentity']['url']; ?>/">
                            <?php echo $item['WithIdentity']['domain'] == NOSERUB_DOMAIN ? $item['WithIdentity']['username'] : $item['WithIdentity']['full_username']; ?>
                        </a>
                    </td>
                    <td><?php echo $item['WithIdentity']['namespace'] == $session->read('Identity.username') ? 'Local' : 'NoseRub'; ?></td>
                    <td>
                        <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contacts/<?php echo $item['Contact']['id']; ?>/delete">Remove Contact</a>
                        <?php if($item['WithIdentity']['namespace'] == $session->read('Identity.username')) { ?>
                             | <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contacts/<?php echo $item['WithIdentity']['id']; ?>/accounts/add/">Add Account</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php }
        } ?>
    </tbody>
</table>
<a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contacts/add/">Add new contact</a>
