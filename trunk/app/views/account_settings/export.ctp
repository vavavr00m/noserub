<?php
header('Content-type: text/javascript');
header('Content-Disposition: attachment; filename="noserub_json"');

App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
$zend_json = new Zend_Json();
$zend_json->useBuiltinEncoderDecoder = true;

echo $zend_json->encode($data);