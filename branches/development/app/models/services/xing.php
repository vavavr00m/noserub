<?php
class XingService extends AbstractService {
	
	public function init() {
	    $this->name = 'Xing';
        $this->url = 'http://xing.com/';
        $this->icon = 'xing.gif';
        $this->has_feed = false;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#xing.com/profile/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'https://www.xing.com/profile/'.$username;
	}
}