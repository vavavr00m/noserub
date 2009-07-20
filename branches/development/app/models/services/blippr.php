<?php
class BlipprService extends AbstractService {
	
	public function init() {
	    $this->name = 'Blippr';
        $this->url = 'http://www.blippr.com';
        $this->service_type_id = 5;
        $this->icon = 'blippr.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#blippr.com/profiles/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.blippr.com/profiles/'.$username;
	}
	
	public function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_content();
        return substr($content, strpos($content, ': ') + 2);
	}
	
public function getFeedUrl($username) {
	    return 'http://www.blippr.com/profiles/'.$username.'/blips.rss';
	    #http://www.blippr.com/profiles/lancew/blips.rss
	}
}