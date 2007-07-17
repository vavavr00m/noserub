<h1>Your accounts</h1>
<table>
    <tr>
        <th>Username</th>
        <th>Service</th>
        <th></th>
    </tr>
    <?php if(!empty($data['Account'])) {
        foreach($data['Account'] as $item) { ?>
            <tr>
                <td><?php echo $item['username']; ?></td>
                <td><?php echo $item['Service']['name']; ?></td>
                <td><a href="/noserub/<?php echo $session->read('Identity.username'); ?>/account/<?php echo $item['id']; ?>/delete/">Delete</a></td>
            </tr>
        <?php }
    } ?>
</table>
<a href="/noserub/<?php echo $session->read('Identity.username'); ?>/account/add/">Add new account</a>

<h1>Your contacts</h1>
<table>
    <tr>
        <th>Username</th>
        <th>Type</th>
        <th></th>
    </tr>
    <?php if(!empty($data['Contact'])) {
        foreach($data['Contact'] as $item) { ?>
            <tr>
                <td><a href="/noserub/<?php echo $item['WithIdentity']['username']; ?>/"><?php echo $item['WithIdentity']['username']; ?></a></td>
                <td><?php echo $item['WithIdentity']['password'] == '' ? 'self' : 'NoseRub'; ?></td>
                <td>
                    <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contact/<?php echo $item['id']; ?>/delete">Delete</a>
                    <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contact/<?php echo $item['WithIdentity']['id']; ?>/account/add/">Add Account</a>
                </td>
            </tr>
        <?php }
    } ?>
</table>
<a href="/noserub/<?php echo $session->read('Identity.username'); ?>/contact/add/">Add new contact</a>