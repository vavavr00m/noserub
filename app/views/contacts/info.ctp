<?php
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
<?php $flashmessage->render(); ?>
<?php if($contact['WithIdentity']['photo']) {
    if(strpos($contact['WithIdentity']['photo'], 'http://') === 0 ||
       strpos($contact['WithIdentity']['photo'], 'https://') === 0) {
        # contains a complete path, eg. from not local identities
        $contact_photo = $contact['WithIdentity']['photo'];
    } else {
        $contact_photo = $static_base_url . $contact['WithIdentity']['photo'].'.jpg';
    }	                
} else {
    $contact_photo = $sex['img'][$contact['WithIdentity']['sex']];
} ?>         
<dl id="hcard-<?php echo $contact['WithIdentity']['local_username']; ?>" class="vcards contacts <?php echo $contact['WithIdentity']['local']==1 ? '' : 'externalcontact'; ?>">
    <dt>
        <a href="<?php echo 'http://' . $contact['WithIdentity']['username']; ?>">
    	    <img class="photo" src="<?php echo $contact_photo; ?>" width="80" height="80" alt="<?php echo $contact['WithIdentity']['single_username']; ?>'s Picture" />
    	</a>
    </dt>                     
    <dt>
        <a class="url nickname" href="<?php echo 'http://' . $contact['WithIdentity']['username']; ?>"><?php echo $contact['WithIdentity']['single_username']; ?></a>
    </dt>
	<dd class="fn"><?php echo $contact['WithIdentity']['name']; ?></dd>
		
	<!-- send e-Mail -->
	<?php if($contact['WithIdentity']['local'] == 1 && $contact['WithIdentity']['allow_emails'] != 0) { ?>
	    <dd class="sendmail">
		    <img src="<?php echo Router::url('/images/icons/services/email.gif'); ?>" height="16" width="16" alt="e-Mail" class="sendmail_icon" /> <a href="http://<?php echo $contact['WithIdentity']['username']; ?>/messages/new/">Send e-Mail</a>
		</dd>
	<?php } ?>
</dl>	
	
<br class="clear" />
<?php if($accounts) { ?>
    <h2>Accounts</h2>
    <ul class="whoissidebar">
        <?php foreach($accounts as $item) { ?>
            <li>
                <?php if(!$item['Account']['service_id']) { ?>
                    <?php echo $item['Account']['account_url']; ?>
                <?php } else { ?>
                    <img src="<?php echo Router::url('/images/icons/services/') . $item['Service']['icon']; ?>" height="16" width="16" alt="<?php echo $item['Service']['name']; ?>" class="whoisicon" />
                    <a rel="me" class="taggedlink" href="<?php echo $item['Account']['account_url']; ?>"><?php echo isset($item['Account']['title']) ? $item['Account']['title'] : $item['Service']['name']; ?></a>
                    <?php if($item['Account']['feed_url']) { ?>
                        <a rel="me" class="taggedlink" href="<?php echo $item['Account']['feed_url']; ?>">
                            <img src="<?php echo Router::url('/images/icons/services/rss.gif') ?>" height="16" width="16" alt="RSS-Feed" class="whoisicon" />
                        </a>
                    <?php } ?>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
<?php } ?>
<a href="<?php echo Router::url('/' . $contact['Identity']['local_username'] . '/contacts/') ?>">Back to list of contacts</a>