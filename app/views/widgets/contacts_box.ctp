<?php
    if($data) {
        $label = __('manage', true);
    } else {
        $label = __('add new', true);
    }
?>
<span class="more">
    <a href="<?php echo Router::Url('/' . $session->read('Identity.local_username') . '/contacts/'); ?>">
        <?php echo $label; ?>
    </a>
</span>
<h4><?php __('My Contacts'); ?></h4>
<?php if($data) { ?>
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
<?php } ?>
    	