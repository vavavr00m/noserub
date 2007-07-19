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
                        <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/accounts/<?php echo $item['Account']['id']; ?>/edit/">Edit Account</a>
                        |
                        <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/accounts/<?php echo $item['Account']['id']; ?>/delete/">Delete Account</a>
                    </td>
                </tr>
            <?php }
        } ?>
    </tbody>
</table>
<a href="/noserub/<?php echo $session->read('Identity.username'); ?>/accounts/add/">Add new account</a>