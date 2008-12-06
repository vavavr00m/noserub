<?php
    $is_self = isset($about_identity['id']) && 
               isset($session_identity) &&
               $about_identity['id'] == $session_identity['id'];
?>
<?php if(isset($about_identity)) {
    echo $this->element('identities/mini_profile', array('data' => $about_identity, 'base_url_for_avatars' => $base_url_for_avatars));
} ?>
    <div class="vcard">
        <?php $flashmessage->render(); ?>
        <?php if($is_self) { ?>
            <?php echo $this->element('identities/what_are_you_doing'); ?>
            <hr class="clear" />
        <?php } ?>
    
        <div>
            <?php echo $this->element('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->element('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>
    </div>
    
    <div id="sidebar">
        <?php if($is_self && $menu['main'] == 'network') {
            echo $this->element('contacts/tag_filter');
            echo $this->element('locations/choose', array('identity' => $about_identity));
            echo '<hr />';
        } ?>
    	<?php echo $this->element('contacts/box', array('box_head' => ($menu['main'] == 'network' ? __('Contacts', true) : __('Latest active', true)), 'data' => $identities, 'static_base_url' => $base_url_for_avatars, 'manage' => ($menu['main'] == 'network' ? true : false))); ?>
    	<?php if(isset($contacts) && $is_self) { ?>
    	    <hr />
    	    <?php echo $this->element('contacts/box', array('box_head' => __('My Contacts', true), 'data' => $contacts, 'static_base_url' => $base_url_for_avatars, 'manage' => true)); ?>
    	<?php } ?>
    	<?php if(isset($newbies)) { ?>
    	    <hr />
            <?php echo $this->element('contacts/box', array('box_head' => __('Newbies', true), 'data' => $newbies, 'static_base_url' => $base_url_for_avatars)); ?>
        <?php } ?>
    </div>