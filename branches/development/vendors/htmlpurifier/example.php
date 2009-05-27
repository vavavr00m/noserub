<?php
require_once 'HTMLpurifier.auto.php';

$dirty_html = '<p><a href="http://www.flickr.com/people/juliajanssen/">Julia Janßen</a> hat ein Foto gepostet:</p>
<p><a href="http://www.flickr.com/photos/juliajanssen/720098879/" title="Streetart"><img src="http://farm2.static.flickr.com/1200/720098879_6a66809296_m.jpg" width="240" height="160" alt="Streetart" /></a></p>';

$config = HTMLPurifier_Config::createDefault();
$config->set('HTML', 'Allowed', 'img[src|alt]');

$purifier = new HTMLPurifier($config);
$clean_html = $purifier->purify($dirty_html);
#$clean_html = str_replace('<img src=', '<img width="75" height="75" src=', $clean_html);
echo '{' . $clean_html . "}\n\n";
?>
