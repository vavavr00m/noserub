<?php
    $session_local_username = isset($session_identity['local_username']) ? $session_identity['local_username'] : '';
    $session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;
    
    $show_action_links  = $about_identity['id']        == $session_identity_id ||
                          ($session_local_username      != '' && 
                           $about_identity['namespace'] == $session_local_username);
?>
<p class="infotext">
    Here you can add all your own social/online activities and import friends in your network.
</p>

<hr class="space" />

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
                    if($item['Service']['is_contact']) {
                        continue;
                    }
                    $username    = isset($item['Account']['username'])    ? $item['Account']['username']    : $item['username'];
                    $account_id  = isset($item['Account']['id'])          ? $item['Account']['id']          : $item['id'];
                    $account_url = isset($item['Account']['account_url']) ? $item['Account']['account_url'] : $item['account_url'];
					$title       = isset($item['Account']['title'])       ? $item['Account']['title']       : false;
                    ?><tr>
                        <?php if($item['Service']['id'] == 8) { ?>
                            <td colspan="2"><a rel="me" href="<?php echo $account_url; ?>"><?php echo $title ? htmlentities($title, ENT_QUOTES, 'UTF-8') : trim(str_replace('http://', '', $account_url), '/'); ?></a></td>


                        <?php } else { ?>
                            <td><a rel="me" href="<?php echo $account_url; ?>"><?php echo $username; ?></a></td>
                            <td><img src="<?php echo Router::url('/images/icons/services/'.$item['Service']['icon']); ?>" alt="<?php echo $item['Service']['name']; ?>" class="whoisicon" /> <?php echo $item['Service']['name']; ?></td>
                        <?php } ?>
                        <td><?php if($show_action_links) {
                        		echo '<ul>';
										  if ($item['Service']['id'] == 8) {
										  echo '<li class="edit icon">' . $html->link('Edit Account', '/' . $about_identity['local_username'] . '/settings/accounts/'.  $account_id . '/edit/') . '</li>';
										  }
                                echo '<li class="delete icon">' . $html->link('Remove Account', '/' . $about_identity['local_username'] . '/settings/accounts/'.  $account_id . '/delete/'.$security_token.'/') . '</li>';
                                echo '</ul>';
                        } ?></td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
<?php } ?>
<?php if($show_action_links) { ?>
	<p class="infotext">
	    <?php echo $html->link('Add new account', '/' . $about_identity['local_username'] . '/settings/accounts/add/', array('class' => 'addmore')); ?>
	</p>
<?php } ?>
