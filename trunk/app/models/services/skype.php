<?php
class SkypeService extends AbstractService {
	
	public function init() {
	    $this->name = 'Skype';
        $this->url = 'http://skype.com/';
        $this->icon = 'skype.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^skype:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'skype:'.$username;
	}
}