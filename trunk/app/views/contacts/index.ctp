<?php
$session_identity_id    = isset($session_identity['id']) ? $session_identity['id'] : 0;

if(empty($noserub_contacts) && empty($private_contacts)) { ?>
    <p>
        No contacts yet.
    </p>
<?php } else {
    if(!empty($noserub_contacts)) {
        echo '<h2>NoseRub Contacts</h2>';
        echo $this->renderElement('contacts/list', array('data' => $noserub_contacts, 'show_photo' => true));
    }
    
    if(!empty($private_contacts)) {
        echo '<h2>Private Contacts</h2>';
        echo $this->renderElement('contacts/list', array('data' => $private_contacts, 'show_photo' => false));
    }
}
if($identity['id'] == $session_identity_id) { ?>
	<br class="clear" />
    <p>
        <?php echo $html->link('Add new contact', '/' . $identity['local_username'] . '/contacts/add/', array('class' => 'addmore')); ?>
    </p>
<?php } ?>