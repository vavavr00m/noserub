<?php
$session_local_username = isset($session_identity['local_username']) ? $session_identity['local_username'] : '';
$session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;

foreach($data as $item) {
    if($item['WithIdentity']['namespace'] != '' && $session_local_username != $item['WithIdentity']['namespace']) {
        # don't display local contacts to anyone else, but the owner
        continue;
    }
    
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
    
    if($item['WithIdentity']['photo']) {
        if(strpos($item['WithIdentity']['photo'], 'http://') === 0 ||
           strpos($item['WithIdentity']['photo'], 'https://') === 0) {
            # contains a complete path, eg. from not local identities
            $contact_photo = $item['WithIdentity']['photo'];
        } else {
            $contact_photo = $base_url_for_avatars . $item['WithIdentity']['photo'].'.jpg';
        }	                
    } else {
    	App::import('Vendor', 'sex');
        $contact_photo = Sex::getImageUrl($item['WithIdentity']['sex']);
    } ?>
                
    <dl id="hcard-<?php echo $item['WithIdentity']['local_username']; ?>" class="vcards <?php echo $show_photo ? 'contacts' : 'private'; ?> <?php echo $item['WithIdentity']['local']==1 ? '' : 'externalcontact'; ?>">
        <?php if($show_photo) { ?>
            <dt>
    	        <a href="<?php echo 'http://' . $item['WithIdentity']['username']; ?>" rel="<?php echo $rel; ?>">
    	            <img class="photo" src="<?php echo $contact_photo; ?>" width="80" height="80" alt="<?php echo $item['WithIdentity']['single_username']; ?>'s Picture" />
    	        </a>
    	    </dt>                     
    	<?php } ?>  			
        <dt>
            <a class="url nickname" href="<?php echo 'http://' . $item['WithIdentity']['username']; ?>" rel="<?php echo $rel; ?>"><?php echo $item['WithIdentity']['single_username']; ?></a>
        </dt>
			<dd class="fn"><?php echo $item['WithIdentity']['name']; ?></dd>
		
		<!-- send e-Mail -->
		<?php if($show_photo && $item['WithIdentity']['local'] == 1 && $item['WithIdentity']['allow_emails'] != 0) { ?>
			    <dd class="sendmail">
			        <img src="<?php echo Router::url('/images/icons/services/email.gif'); ?>" height="16" width="16" alt="e-Mail" class="sendmail_icon" /> <a href="http://<?php echo $item['WithIdentity']['username']; ?>/messages/new/"><?php __('Send e-Mail'); ?></a>
			    </dd>
			<?php } ?>

        <?php 
            $is_private = ($session_local_username != '' && $item['WithIdentity']['namespace'] == $session_local_username);
                
            $identity_id = isset($item['Contact']['identity_id']) ? $item['Contact']['identity_id'] : $item['identity_id'];
            if($identity_id == $session_identity_id && $session_identity_id != 0) { ?>
                <?php if(!$is_private && !$item['WithIdentity']['local']) { ?>
                    <dd class="contact_option"><?php echo $html->link(__('Info', true), '/' . $session_local_username . '/contacts/' . (isset($item['Contact']['id']) ? $item['Contact']['id'] : $item['id']) . '/info/'); ?></dd>
                <?php } ?>
                <dd class="contact_option"><?php echo $html->link(__('Remove Contact', true), '/' . $session_local_username . '/contacts/' . (isset($item['Contact']['id']) ? $item['Contact']['id'] : $item['id']) . '/delete/'.$security_token.'/'); ?></dd>
                <dd class="contact_option"><?php echo $html->link(__('Edit Contact', true), '/' . $session_local_username . '/contacts/' . (isset($item['Contact']['id']) ? $item['Contact']['id'] : $item['id']) . '/edit/'); ?></dd>
            <?php } ?>
            <?php if($is_private) { ?>
                <dd><?php echo $html->link(__('Manage Services', true), '/settings/accounts/'); ?></dd>
                <dd><?php echo $html->link(__('Add Service', true), '/settings/accounts/add/'); ?></dd>
            <?php } ?>
	</dl>
<?php }