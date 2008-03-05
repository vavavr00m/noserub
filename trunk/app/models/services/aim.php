<?php
class AimService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^aim:goIM\?screenname=(.+)/'));
	}
	
	function getAccountUrl($username) {
		return 'aim:goIM?screenname='.$username;
	}
}
?>