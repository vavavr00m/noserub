<?php
vendor('Zend/Json');
$zend_json = new Zend_Json();
$zend_json->useBuiltinEncoderDecoder = true;

echo $zend_json->encode($data);
?>