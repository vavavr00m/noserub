<?php
header('Content-type: text/javascript');
header('Content-Disposition: attachment; filename="noserub_json"');

App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));

echo Zend_Json::encode($data);