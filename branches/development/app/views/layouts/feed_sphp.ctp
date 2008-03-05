<?php
# go through all the items and build the json data structure
$items = array();
foreach($data as $item) {
    $items[] = array('when'    => $nicetime->show($item['datetime']),
                     'what'    => ucfirst($item['type']),
                     'title'   => $item['title'],
                     'link'    => $item['url'],
                     'content' => $item['content']);
}
echo serialize($items);
?>