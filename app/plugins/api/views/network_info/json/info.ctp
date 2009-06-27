<?php 
App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
echo Zend_Json::encode(array('data' => $data));
?>