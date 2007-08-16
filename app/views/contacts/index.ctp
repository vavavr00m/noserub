<h1>Your contacts</h1>
<?php echo $this->renderElement('contacts/index'); ?>
<?php echo $html->link('Add new contact', '/' . $session->read('Identity.username') . '/contacts/add/'); ?>
