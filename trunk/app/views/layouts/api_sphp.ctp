<?php
$sphp = array('data' => isset($data) ? $data : array(),
              'code' => isset($code) ? $code : 0,
               'msg' => isset($msg)  ? $msg  : 'ok');
echo serialize($sphp);
?>