<?php
class ZooomrService extends AbstractService {
	
	public function init() {
	    $this->name = 'Zooomr';
        $this->url = 'http://zooomr.com/';
        $this->service_type = 1;
        $this->icon = 'zooomr.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#zooomr.com/photos/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.zooomr.com/photos/'.$username;
	}
	
	public function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        if(preg_match('/<img src="(.*)_m\.jpg"/iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'_s.jpg" /></a>';
        }
        return '';
	}
	
	public function getFeedUrl($username) {
		return 'http://www.zooomr.com/services/feeds/public_photos/?id='.$username.'&format=rss_200';
	}
}