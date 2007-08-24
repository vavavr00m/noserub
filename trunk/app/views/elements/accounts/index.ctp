<?php
    $is_logged_in_user = $identity['id'] == $session->read('Identity.id');
?>
<?php if(empty($data)) { ?>
    <p>
        No accounts yet.
    </p>
<?php } else {?>
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
                foreach($data as $item) {
                    $username    = isset($item['Account']['username'])    ? $item['Account']['username'] : $item['username'];
                    $account_id  = isset($item['Account']['id'])          ? $item['Account']['id'] : $item['id'];
                    $account_url = isset($item['Account']['account_url']) ? $item['Account']['account_url'] : $item['account_url'];
                    ?><tr>
                        <?php if($item['Service']['id'] == 8) { ?>
                            <td colspan="2"><?php echo trim(str_replace('http://', '', $account_url), '/'); ?></td>
                        <?php } else { ?>
                            <td><?php echo $username; ?></td>
                            <td><?php echo $item['Service']['name']; ?></td>
                        <?php } ?>
                        <td><?php if($is_logged_in_user) {
                            echo $html->link('Edit Account', '/' . $session->read('Identity.username') . '/accounts/'.  $account_id . '/edit/');
                            echo ' | ';
                            echo $html->link('Delete Account', '/' . $session->read('Identity.username') . '/accounts/'.  $account_id . '/delete/');
                        } ?></td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
<?php } ?>
<?php if($is_logged_in_user) {
    echo $html->link('Add new account', '/' . $identity['username'] . '/accounts/add/'); 
} ?>