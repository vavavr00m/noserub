<?php 
App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
$json = Zend_Json::encode(array('data' => $data));

if (!isset($this->params['url']['callback'])) {
	echo $json;
} else {
	echo $this->params['url']['callback'] . '(' . $json . ')';
}
?>