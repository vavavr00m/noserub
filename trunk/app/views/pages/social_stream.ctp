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

    <div class="vcard">
        <div>
            <h4>Social activity</h4>
            <?php echo $this->renderElement('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>
    </div>

    <div id="sidebar">	
    	<h4>Profiles</h4>
    	<p class="friendthumbs">
    	    <?php foreach($identities as $item) { ?>
    	        <a href="http://<?php echo $item['username']; ?>" rel="friend">
    	            <?php if($item['photo']) {
    	                if(strpos($item['photo'], 'http://') === 0 ||
                           strpos($item['photo'], 'https://') === 0) {
                            # contains a complete path, eg. from not local identities
                            $photo_url = $item['photo'];
                            $contact_photo = str_replace('.jpg', '-small.jpg', $photo_url);
    	                } else {
    	                    $contact_photo = $static_base_url . $item['photo'].'-small.jpg';
    	                }	                
                    } else {
                        $contact_photo = $sex['img-small'][$item['sex']];
                    } ?>
    	            <img src="<?php echo $contact_photo; ?>" width="35" height="35" alt="<?php echo $item['local_username']; ?>'s Picture" />
    	        </a>
    	    <?php } ?>
    	</p>
        <hr />
    </div>