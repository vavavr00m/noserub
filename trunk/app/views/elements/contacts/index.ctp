<?php
    $session_local_username = isset($session_identity['local_username']) ? $session_identity['local_username'] : '';
    $session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;
    
     $sex = array('img' => array(0 => Router::url('/images/profile/avatar/noinfo.gif'),
                                    1 => Router::url('/images/profile/avatar/female.gif'),
                                    2 => Router::url('/images/profile/avatar/male.gif')),
                     'img-small' => array(0 => Router::url('/images/profile/avatar/noinfo-small.gif'),
                                          1 => Router::url('/images/profile/avatar/female-small.gif'),
                                          2 => Router::url('/images/profile/avatar/male-small.gif')),
                     'he' => array(0 => 'he/she',
                                   1 => 'she',
                                   2 => 'he'),
                     'him' => array(0 => 'him/her',
                                    1 => 'her',
                                    2 => 'him'));
                                    
    if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
        $static_base_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/avatars/';
    } else {
        $static_base_url = FULL_BASE_URL . Router::url('/static/avatars/');
    }
?>

<?php if(empty($data)) { ?>
        <p>
            No contacts yet.
        </p>
<?php } else { ?>
    <table class="listing">
        <thead>
            <tr>
                <th>Contact</th>
                <th>Type</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($data)) {
                foreach($data as $item) { 
                    if($item['WithIdentity']['namespace'] != '' && $session_local_username != $item['WithIdentity']['namespace']) {
                        # don't display local contacts to anyone else, but the owner
                        continue;
                    }
                    ?>
                    <tr>
                        <td>
                            <?php if($item['WithIdentity']['photo']) {
            	                if(strpos($item['WithIdentity']['photo'], 'http://') === 0 ||
                                   strpos($item['WithIdentity']['photo'], 'https://') === 0) {
                                    # contains a complete path, eg. from not local identities
                                    $photo_url = $item['WithIdentity']['photo'];
                                    $contact_photo = str_replace('.jpg', '-small.jpg', $photo_url);
            	                } else {
            	                    $contact_photo = $static_base_url . $item['WithIdentity']['photo'].'-small.jpg';
            	                }	                
                            } else {
                                $contact_photo = $sex['img-small'][$item['WithIdentity']['sex']];
                            } ?>
            	            <img src="<?php echo $contact_photo; ?>" width="35" height="35" alt="<?php echo $item['WithIdentity']['local_username']; ?>'s Picture" />            	            
                            <?php echo $html->link($item['WithIdentity']['local_username'], 'http://' . $item['WithIdentity']['username']); ?>
                        </td>
                        <td><?php
                            if(($session_local_username != '' && $session_local_username == $item['WithIdentity']['namespace']) ||
                               ($session_local_username == '' && $item['WithIdentity']['namespace'] != '')) {
                                echo 'Private';
                            } else {
                                echo 'NoseRub';
                            } 
                        ?></td>
                        <td>
                            <?php 
                                $identity_id = isset($item['Contact']['identity_id']) ? $item['Contact']['identity_id'] : $item['identity_id'];
                                if($identity_id == $session_identity_id && $session_identity_id != 0) { ?>
                                <?php echo $html->link('Remove Contact', '/' . $session_local_username . '/contacts/' . (isset($item['Contact']['id']) ? $item['Contact']['id'] : $item['id']) . '/delete'); ?>
                            <?php } ?>
                            <?php if($session_local_username != '' && $item['WithIdentity']['namespace'] == $session_local_username) { ?>
                                | <?php echo $html->link('Add Account', '/' . $item['WithIdentity']['local_username'] . '/accounts/add/'); ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
<?php } ?>
<?php if($identity['id'] == $session_identity_id) { ?>
    <p class="infotext">
        <?php echo $html->link('Add new contact', '/' . $identity['local_username'] . '/contacts/add/', array('class' => 'addmore')); ?>
    </p>
<?php } ?>