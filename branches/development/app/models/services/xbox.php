<?php
class XBoxService extends AbstractService {
	
	public function init() {
	    $this->name = 'XBox Live';
        $this->url = 'http://xbox.com/';
        $this->icon = 'xbox.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#live.xbox.com/member/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://live.xbox.com/member/' . $username;
	}
}