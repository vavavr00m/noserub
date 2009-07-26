<?php 

echo $javascript->link('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
if(Context::googleMapsKey() && Context::showMap()) {
    echo $javascript->link('http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . Context::googleMapsKey());
}
echo $javascript->object(Context::forJs(), array('prefix' => 'var noserub_context=', 'block' => true));

echo $javascript->link('theme.js');
echo $javascript->link('noserub.js');

?>