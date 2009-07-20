<?php
class SkitchService extends AbstractService {
	
	public function init() {
	    $this->name = 'Skitch';
        $this->url = 'http://skitch.com/';
        $this->service_type_id = 1;
        $this->icon = 'skitch.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#skitch.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://skitch.com/' . $username . '/';
	}
	
	public function getContent($feeditem) {
		$content = $feeditem->get_content();
        return $content;
	}
	
	public function getFeedUrl($username) {
	    return 'http://skitch.com/feeds/' . $username . '/atom.xml';
	}
}