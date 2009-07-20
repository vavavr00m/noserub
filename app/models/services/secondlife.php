<?php
class SecondlifeService extends AbstractService {
	
	public function init() {
	    $this->name = 'Second Life';
        $this->url = 'http://secondlife.com/';
        $this->icon = 'sl.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^#(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return '#'.$username;
	}	
}