<?php

echo $form->create('Identity', array('url' => '/pages/switch/language/'));
?>

<label for="ConfigLanguage"><?php echo __('Language', true) ?></label>

<?php
$languages = Configure::read('Languages');
$session_language = $session->read('Config.language');
echo $form->select('Config.language', $languages, $session_language, array(), false);

echo $form->end(array('label' => __('OK', true)));
