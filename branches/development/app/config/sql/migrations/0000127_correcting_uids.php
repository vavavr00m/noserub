<?php

$url = Router::url('/entry/', true);
$this->query('UPDATE entries SET url=CONCAT("' . $url .'", id, "/"), uid=MD5(CONCAT("' . $url .'", id, "/")) WHERE uid="" OR url=""');