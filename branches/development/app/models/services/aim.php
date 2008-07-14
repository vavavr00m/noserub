<?php
class AimService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^aim:goIM\?screenname=(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'aim:goIM?screenname='.$username;
	}
}
?>