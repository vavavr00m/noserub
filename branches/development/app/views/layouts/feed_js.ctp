<?php
App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
$zend_json = new Zend_Json();
$zend_json->useBuiltinEncoderDecoder = true;

# go through all the items and build the json data structure
$items = array();
foreach($data as $item) {
    $items[] = array('when'    => $nicetime->show($item['datetime']),
                     'what'    => ucfirst($item['type']),
                     'title'   => $item['title'],
                     'link'    => $item['url'],
                     'content' => $item['content']);
}
echo 'noserub_feed='.$zend_json->encode($items);
?>