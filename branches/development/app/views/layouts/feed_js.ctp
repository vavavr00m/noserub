<?php
App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
Zend_Json::$useBuiltinEncoderDecoder = true;

# go through all the items and build the json data structure
$items = array();
foreach($data as $item) {
    $items[] = array('when'    => $nicetime->show($item['Entry']['published_on']),
                     'what'    => ucfirst($item['ServiceType']['token']),
                     'title'   => $item['Entry']['title'],
                     'link'    => $item['Entry']['url'],
                     'content' => $item['Entry']['content']);
}
echo 'noserub_feed='.Zend_Json::encode($items);
?>