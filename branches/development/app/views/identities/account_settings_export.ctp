<?php
header('Content-type: text/javascript');
header('Content-Disposition: attachment; filename="noserub_export.json"');

vendor('Zend/Json');
$zend_json = new Zend_Json();
$zend_json->useBuiltinEncoderDecoder = true;

echo $zend_json->encode($data);