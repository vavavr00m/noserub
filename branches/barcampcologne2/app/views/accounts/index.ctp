<h1>Your accounts</h1>
<?php echo $this->renderElement('accounts/index'); ?>
<?php echo $html->link('Add new account', '/' . $session->read('Identity.username') . '/accounts/add'); ?>