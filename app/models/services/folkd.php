<?php
class FolkdService extends AbstractService {
	
	public function init() {
	    $this->name = 'Folkd';
        $this->url = 'http://www.folkd.com/';
        $this->service_type = 2;
        $this->icon = 'folkd.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#folkd.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.folkd.com/user/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.folkd.com/rss.php?items=15&find=all&sort=&user='.$username;
	}
}