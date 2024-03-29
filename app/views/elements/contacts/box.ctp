<?php App::import('Vendor', 'UrlUtil'); ?>
<?php if (isset($manage) && $manage && $session->check('Identity')): ?>
<span class="more"><?php echo $html->link(__('manage', true), '/'.$session->read('Identity.local_username').'/contacts'); ?></span>
<?php endif; ?>
<h4>
	<?php echo $box_head; ?>
</h4>
<p class="contactsbox">
    <?php if($data) { ?>
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
                        $contact_photo = $static_base_url . $item['photo'].'-small.jpg';
                    }	                
                } else {
                    App::import('Vendor', 'sex');
                	$contact_photo = Sex::getSmallImageUrl($item['sex']);
                } ?>
                <img src="<?php echo $contact_photo; ?>" width="35" height="35" alt="<?php echo $item['local_username']; ?>'s Picture" class="<?php echo $item['local']==1 ? 'internthumbs' : 'externthumbs'; ?>" />
            </a>
        <?php } ?>
    <?php } ?>
</p>