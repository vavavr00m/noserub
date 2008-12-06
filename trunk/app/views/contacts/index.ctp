<?php $flashmessage->render(); ?>
<?php
$session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;

if(empty($noserub_contacts) && empty($private_contacts)) { ?>
    <p class="infotext">
        <?php __('No contacts yet.'); ?>
    </p>
<?php } else {
    echo $this->element('contacts/tag_filter');
    
	if($identity['id'] == $session_identity_id) {
    	echo '<p class="infotext">';
        echo $html->link(__('Add new contact', true), '/' . $identity['local_username'] . '/contacts/add/', array('class' => 'addmore'));
    	echo '</p>';
	}
    if(!empty($noserub_contacts)) {
        echo '<h3 class="contactsheadline">' . __('NoseRub Contacts', true) . '</h3>';
        echo $this->element('contacts/list', array('data' => $noserub_contacts, 'show_photo' => true, 'base_url_for_avatars' => $base_url_for_avatars));
    }
    
    if(!empty($private_contacts)) {
        echo '<br class="clear" /><h3 class="contactsheadline">' . __('Private Contacts', true) . '</h3>';
        echo $this->element('contacts/list', array('data' => $private_contacts, 'show_photo' => false, 'base_url_for_avatars' => $base_url_for_avatars));
    }
}
if($identity['id'] == $session_identity_id) { ?>
	<br class="clear" />
    <p class="infotext">
        <?php echo $html->link(__('Add new contact', true), '/' . $identity['local_username'] . '/contacts/add/', array('class' => 'addmore')); ?>
    </p>
<?php } ?>