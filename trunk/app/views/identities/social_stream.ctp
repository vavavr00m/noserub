<?php if(isset($about_identity)) {
    echo $this->element('identities/mini_profile', array('data' => $about_identity, 'base_url_for_avatars' => $base_url_for_avatars));
} ?>
    <div class="vcard">
    
        <?php if(isset($about_identity['id']) && 
                 isset($session_identity) &&
                 $about_identity['id'] == $session_identity['id']) { ?>
            <?php echo $this->element('identities/what_are_you_doing'); ?>
            <hr class="clear" />
        <?php } ?>
    
        <div>
            <h4>Social activity</h4>
            <?php echo $this->element('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->element('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>
    </div>
    
    <div id="sidebar">
        <?php if($menu['main'] == 'network') {
            echo $this->element('contacts/tag_filter');
        } ?>
    	<?php echo $this->element('contacts/box', array('box_head' => ($menu['main'] == 'network' ? 'Contacts' : 'Latest active'), 'data' => $identities, 'static_base_url' => $base_url_for_avatars, 'manage' => ($menu['main'] == 'network' ? true : false))); ?>
    	<?php if($menu['logged_in'] && isset($contacts)) { ?>
    	    <hr />
    	    <?php echo $this->element('contacts/box', array('box_head' => 'My Contacts', 'data' => $contacts, 'static_base_url' => $base_url_for_avatars, 'manage' => true)); ?>
    	<?php } ?>
    	<?php if(isset($newbies)) { ?>
    	    <hr />
            <?php echo $this->element('contacts/box', array('box_head' => 'Newbies', 'data' => $newbies, 'static_base_url' => $base_url_for_avatars)); ?>
        <?php } ?>
    </div>