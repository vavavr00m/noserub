<?php

echo $form->create('Identity', array('url' => '/pages/switch/language/'));
echo __('Language', true) . ': '; 

$languages = Configure::read('Languages');
$session_language = $session->read('Config.language');
echo $form->select('Config.language', $languages, $session_language, array(), false);

echo $form->end(array('label' => __('OK', true)));