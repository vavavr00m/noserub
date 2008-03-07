<?php
	include('hkit.class.php');
	
	$h	= new hKit;
	
	// Config options (see top of class file)
	$h->tidy_mode	= 'none'; // 'proxy', 'exec', 'php' or 'none'
	
	// Get by URL
	$result	= $h->getByURL('hcard', $argv[1]);

	// Get by String
	//$result	= $h->getByString('hcard', '<div class="vcard"><p class="fn">Drew McLellan</p></div>');

	print '<pre>'.print_r($result, 1).'</pre>';
?>
