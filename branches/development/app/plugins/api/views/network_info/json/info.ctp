<?php 
App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));

$json = array('data' => isset($data) ? $data : array());
echo Zend_Json::encode($json);
?>