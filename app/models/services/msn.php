<?php
class MsnService extends AbstractService {
	
	public function init() {
	    $this->name = 'MSN';
        $this->url = 'http://im.live.com/';
        $this->icon = 'msn.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^msnim:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'msnim:'.$username;
	}
}