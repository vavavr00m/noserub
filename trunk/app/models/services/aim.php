<?php
class AimService extends AbstractService {
	
	public function init() {
	    $this->name = 'AIM';
        $this->url = 'http://aim.com';
        $this->service_type = 5;
        $this->icon = 'aim.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^aim:goIM\?screenname=(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'aim:goIM?screenname='.$username;
	}
}