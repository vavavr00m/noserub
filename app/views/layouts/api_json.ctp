<?php
App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));

$json = array('data' => isset($data) ? $data : array(),
              'code' => isset($code) ? $code : 0,
               'msg' => isset($msg)  ? $msg  : 'ok');
echo Zend_Json::encode($json);
?>