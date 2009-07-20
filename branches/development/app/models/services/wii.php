<?php
class WiiService extends AbstractService {
	
	public function init() {
	    $this->name = 'Wii';
        $this->url = 'http://wii.com';
        $this->icon = 'wii.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^wii:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'wii:' . $username;
	}
}