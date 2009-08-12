<?php 

echo $html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
if(Context::googleMapsKey() && Context::showMap()) {
    echo $html->script('http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . Context::googleMapsKey());
}
echo $javascript->object(Context::forJs(), array('prefix' => 'var noserub_context=', 'block' => true));

echo $html->script('theme.js');
echo $html->script('noserub.js');

?>