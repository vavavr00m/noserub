<h1>Your accounts</h1>
<table class="listing">
    <thead>
        <tr>
            <th>Username</th>
            <th>Service</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($data)) {
            foreach($data as $item) { ?>
                <tr>
                    <td><?php echo $item['Account']['username']; ?></td>
                    <td><?php echo $item['Service']['name']; ?></td>
                    <td>
                        <?php echo $html->link('Edit Account', '/' . $session->read('Identity.username') . '/accounts/'.  $item['Account']['id'] . '/edit/'); ?>
                        |
                        <?php echo $html->link('Delete Account', '/' . $session->read('Identity.username') . '/accounts/'.  $item['Account']['id'] . '/delete/'); ?>
                    </td>
                </tr>
            <?php }
        } ?>
    </tbody>
</table>
<?php echo $html->link('Add new account', '/' . $session->read('Identity.username') . '/accounts/add'); ?>