<?php
class ScratchService extends AbstractService {
	
	public function init() {
	    $this->name = 'Scratch';
        $this->url = 'http://scratch.mit.edu/';
        $this->service_type_id = 5;
        $this->icon = 'scratch.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#scratch.mit.edu/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://scratch.mit.edu/users/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		App::import('Vendor', 'WebExtractor');
        $content = WebExtractor::fetchUrl('http://scratch.mit.edu/users/'.$username.'/');
        if(preg_match('/getRecentUserProjects\/(.*)\"/i', $content, $matches)) {
        	return 'http://scratch.mit.edu/feeds/getRecentUserProjects/'.$matches[1];
        } else {
        	return false;
        }

		
	}
}



