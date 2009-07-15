<?php App::import('Vendor', 'UrlUtil'); ?>
<div class="block-friends">
    <ul>
    <?php foreach($data as $item) { ?>
        <li>
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
                if(UrlUtil::startsWithHttpOrHttps($item['photo'])) {
                    # contains a complete path, eg. from not local identities
                    $contact_photo = $item['photo'];
                } else {
                    $contact_photo = $noserub->fnAvatarBaseUrl() . $item['photo'].'-medium.jpg';
                }	                
            } else {
                App::import('Vendor', 'sex');
            	$contact_photo = Sex::getMediumImageUrl($item['sex']);
            } ?>
            <?php
                $local_username = isset($item['local_username']) ? $item['local_username'] : $item['username'];
                $local = isset($item['local']) ? $item['local'] : 1;
            ?>
            <img src="<?php echo $contact_photo; ?>" width="62" height="62" alt="<?php echo $local_username; ?>'s Picture" class="<?php echo $local==1 ? 'internthumbs' : 'externthumbs'; ?>" />
        </a>
        </li>
    <?php } ?>
    </ul>
    <p class="more">
		<a href="#"><?php __('show more'); ?></a>
	</p>
</div>