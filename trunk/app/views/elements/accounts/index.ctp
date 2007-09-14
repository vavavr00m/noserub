<?php
    $session_local_username = isset($session_identity['local_username']) ? $session_identity['local_username'] : '';
    $session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;
    
    $show_action_links  = $about_identity['id']        == $session_identity_id ||
                          ($session_local_username      != '' && 
                           $about_identity['namespace'] == $session_local_username);
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
                    $username    = isset($item['Account']['username'])    ? $item['Account']['username']    : $item['username'];
                    $account_id  = isset($item['Account']['id'])          ? $item['Account']['id']          : $item['id'];
                    $account_url = isset($item['Account']['account_url']) ? $item['Account']['account_url'] : $item['account_url'];
                    ?><tr>
                        <?php if($item['Service']['id'] == 8) { ?>
                            <td colspan="2"><?php echo trim(str_replace('http://', '', $account_url), '/'); ?></td>
                        <?php } else { ?>
                            <td><?php echo $username; ?></td>
                            <td><?php echo $item['Service']['name']; ?></td>
                        <?php } ?>
                        <td><?php if($show_action_links) {
                                echo $html->link('Edit Account', '/' . $about_identity['local_username'] . '/accounts/'.  $account_id . '/edit/');
                                echo ' | ';
                                echo $html->link('Delete Account', '/' . $about_identity['local_username'] . '/accounts/'.  $account_id . '/delete/');
                        } ?></td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
<?php } ?>
<?php if($show_action_links) {
    echo $html->link('Add new account', '/' . $about_identity['local_username'] . '/accounts/add/'); 
} ?>