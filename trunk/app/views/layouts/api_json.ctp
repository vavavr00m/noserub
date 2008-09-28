<?php
App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
$zend_json = new Zend_Json();
$zend_json->useBuiltinEncoderDecoder = true;

$json = array('data' => isset($data) ? $data : array(),
              'code' => isset($code) ? $code : 0,
               'msg' => isset($msg)  ? $msg  : 'ok');
echo $zend_json->encode($json);
?>