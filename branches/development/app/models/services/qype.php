<?php
class QypeService extends AbstractService {
	
	public function init() {
	    $this->name = 'Qype';
        $this->url = 'http://www.qype.com/';
        $this->service_type_id = 3;
        $this->icon = 'qype.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#qype.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/rss';
	}
}