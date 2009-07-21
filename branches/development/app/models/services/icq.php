<?php
class IcqService extends AbstractService {
	
	public function init() {
	    $this->name = 'ICQ';
        $this->url = 'http://icq.com/';
        $this->service_type = 5;
        $this->icon = 'icq.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#icq.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.icq.com/'.$username;
	}
}