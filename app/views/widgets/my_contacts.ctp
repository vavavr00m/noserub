<?php if($data) { ?>
    <span class="more"><a href="<?php echo Router::Url('/' . $session->read('Identity.local_username') . '/contacts/'); ?>"><?php __('manage'); ?></a></span>
    <h4><?php __('My Contacts'); ?></h4>
    <p class="contactsbox">
        <?php foreach($data as $item) { ?>
            <?php 
            
            if(isset($item['NoserubContactType']) && $item['NoserubContactType']) {
                $rel_data = array();
                foreach($item['NoserubContactType'] as $contact_type) {
                    if($contact_type['is_xfn']) {
                        $rel_data[] = $contact_type['name'];
                    }
                }
                if($rel_data) {
                    $rel = join(' ', $rel_data);
                } else {
                    $rel = 'contact';
                }
            } else {
                $rel = 'contact';
            }
    
            if(isset($item['WithIdentity'])) {
                $item = $item['WithIdentity'];
            } else if(isset($item['Identity'])) {
                $item = $item['Identity'];
            }
    
            ?>
            <a href="http://<?php echo $item['username']; ?>" rel="<?php echo $rel; ?>">
                <?php if($item['photo']) {
                    if (UrlUtil::startsWithHttpOrHttps($item['photo'])) {
                        # contains a complete path, eg. from not local identities
                        $contact_photo = $item['photo'];
                    } else {
                        $contact_photo = $noserub->fnAvatarBaseUrl() . $item['photo'].'-small.jpg';
                    }	                
                } else {
                    App::import('Vendor', 'sex');
                	$contact_photo = Sex::getSmallImageUrl($item['sex']);
                } ?>
                <img src="<?php echo $contact_photo; ?>" width="35" height="35" alt="<?php echo $item['local_username']; ?>'s Picture" class="<?php echo $item['local']==1 ? 'internthumbs' : 'externthumbs'; ?>" />
            </a>
        <?php } ?>
    </p>
    <p class="morefriends">
		<strong><?php echo $num_noserub_contacts; ?></strong> NoseRub contacts<br />
		<strong><?php echo $num_private_contacts; ?></strong> private contacts
		<?php if($num_noserub_contacts+$num_private_contacts > 0 || $num_noserub_contacts > 0) { ?>
		    <a href="<?php echo Router::Url('/' . $session->read('Identity.local_username') . '/network/'); ?>"><?php __("Contact's Social Stream"); ?></a>
		<?php } ?>
    </p>
<?php } ?>
    	