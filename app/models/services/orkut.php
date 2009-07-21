<?php
class OrkutService extends AbstractService {
	
	public function init() {
	    $this->name = 'Orkut';
        $this->url = 'http://www.orkut.com/';
        $this->service_type = 5;
        $this->icon = 'orkut.gif';
        $this->has_feed = false;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#orkut.com/Profile.aspx\?uid=(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.orkut.com/Profile.aspx?uid='.$username;
	}
}