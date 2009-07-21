<?php
class LinkedinService extends AbstractService {
	
	public function init() {
	    $this->name = 'LinkedIn';
        $this->url = 'http://linkedin.com/';
        $this->service_type = 5;
        $this->icon = 'linkedin.gif';
        $this->has_feed = false;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#www.linkedin.com/in/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.linkedin.com/in/'.$username;
	}
}