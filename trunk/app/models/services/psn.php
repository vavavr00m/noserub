<?php
class PsnService extends AbstractService {
	
	public function init() {
	    $this->name = 'Playstation Network';
        $this->url = 'http://playstation.com/';
        $this->icon = 'psn.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#^http://profiles.us.playstation.com/playstation/psn/visit/profiles/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://profiles.us.playstation.com/playstation/psn/visit/profiles/' . $username . '/';
	}
}