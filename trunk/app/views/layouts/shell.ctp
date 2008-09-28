<?php 
echo "\n- - - - running: " . $_SERVER['REQUEST_URI'] . "- - - - - - - - - -\n";
echo 'start: ' . SHELL_START_TIMESTAMP . "\n\n";
echo strip_tags($content_for_layout);
echo "\n\nend: " . date('Y-m-d H:i:s') . "\n";
echo "\n\n- - - - - - - - - - - - - - - - - - - - - - - - - - - -\n";
?>