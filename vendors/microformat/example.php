<?php
	include('hcard.php');
	include('xfn.php');
	
	$hcard	= new hcard;
	$hcard	= $hcard->getByURL($argv[1]);
	print_r($hcard);

	$xfn = new xfn;
	$xfn = $xfn->getByUrl($argv[1]);
	print_r($xfn);
?>
