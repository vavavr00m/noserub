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

<?php if(isset($about_identity)) {
    echo $this->renderElement('identities/mini_profile', array('data' => $about_identity));
} ?>

    <div class="vcard">
        <div>
            <h4>Social activity</h4>
            <?php echo $this->renderElement('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>
    </div>

    <div id="sidebar">
    	<?php echo $this->renderElement('contacts/box', array('box_head' => ($menu['main'] == 'network' ? 'Contacts' : 'Latest active'), 'sex' => $sex, 'data' => $identities, 'static_base_url' => $static_base_url)); ?>
    	<?php if($menu['logged_in'] && isset($contacts)) { ?>
    	    <hr />
    	    <?php echo $this->renderElement('contacts/box', array('box_head' => 'My Contacts', 'sex' => $sex, 'data' => $contacts, 'static_base_url' => $static_base_url)); ?>
    	<?php } ?>
    </div>