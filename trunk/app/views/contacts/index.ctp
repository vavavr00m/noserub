<?php $flashmessage->render(); ?>
<?php
$session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;

if(empty($noserub_contacts) && empty($private_contacts)) { ?>
    <p class="infotext">
        No contacts yet.
    </p>
<?php } else {

	if($identity['id'] == $session_identity_id) {
    	echo '<p class="infotext">';
        echo $html->link('Add new contact', '/' . $identity['local_username'] . '/contacts/add/', array('class' => 'addmore'));
    	echo '</p>';
	}
    if(!empty($noserub_contacts)) {
        echo '<h3 class="contactsheadline">NoseRub Contacts</h3>';
        echo $this->renderElement('contacts/list', array('data' => $noserub_contacts, 'show_photo' => true));
    }
    
    if(!empty($private_contacts)) {
        echo '<br class="clear" /><h3 class="contactsheadline">Private Contacts</h3>';
        echo $this->renderElement('contacts/list', array('data' => $private_contacts, 'show_photo' => false));
    }
}
if($identity['id'] == $session_identity_id) { ?>
	<br class="clear" />
    <p class="infotext">
        <?php echo $html->link('Add new contact', '/' . $identity['local_username'] . '/contacts/add/', array('class' => 'addmore')); ?>
    </p>
<?php } ?>