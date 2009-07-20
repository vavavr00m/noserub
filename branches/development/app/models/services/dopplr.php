<?php
class DopplrService extends AbstractService {
	
	public function init() {
	    $this->name = 'Dopplr';
        $this->url = 'http://www.dopplr.com/';
        $this->service_type_id = 5;
        $this->icon = 'dopplr.gif';
        $this->has_feed = true;
	}
	
	public function getMinutesBetweenUpdates() {
	    return 30;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#dopplr.com/traveller/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.dopplr.com/traveller/'.$username;
	}
}