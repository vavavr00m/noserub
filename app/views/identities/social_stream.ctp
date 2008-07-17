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
?>

<?php if(isset($about_identity)) {
    echo $this->element('identities/mini_profile', array('data' => $about_identity, 'base_url_for_avatars' => $base_url_for_avatars));
} ?>

    <div class="vcard">
        <div>
            <h4>Social activity</h4>
            <?php echo $this->element('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->element('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>
    </div>

    <div id="sidebar">
    	<?php echo $this->element('contacts/box', array('box_head' => ($menu['main'] == 'network' ? 'Contacts' : 'Latest active'), 'sex' => $sex, 'data' => $identities, 'static_base_url' => $base_url_for_avatars, 'manage' => ($menu['main'] == 'network' ? true : false))); ?>
    	<?php if($menu['logged_in'] && isset($contacts)) { ?>
    	    <hr />
    	    <?php echo $this->element('contacts/box', array('box_head' => 'My Contacts', 'sex' => $sex, 'data' => $contacts, 'static_base_url' => $base_url_for_avatars, 'manage' => true)); ?>
    	<?php } ?>
    	<?php if(isset($newbies)) { ?>
    	    <hr />
            <?php echo $this->element('contacts/box', array('box_head' => 'Newbies', 'sex' => $sex, 'data' => $newbies, 'static_base_url' => $base_url_for_avatars)); ?>
        <?php } ?>
    </div>