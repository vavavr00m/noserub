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
                    <td><a href="<?php echo $item['WithIdentity']['url']; ?>/"><?php echo $item['WithIdentity']['username']; ?></a></td>
                    <td><?php echo $item['WithIdentity']['password'] == '' ? 'self' : 'NoseRub'; ?></td>
                    <td>
                        <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contacts/<?php echo $item['Contact']['id']; ?>/delete">Delete Contact</a> | 
                        <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contacts/<?php echo $item['WithIdentity']['id']; ?>/accounts/add/">Add Account</a>
                    </td>
                </tr>
            <?php }
        } ?>
    </tbody>
</table>
<a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contacts/add/">Add new contact</a>