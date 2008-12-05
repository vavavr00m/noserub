<?php
class IpernityService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ipernity.com/doc/(.+)/home/photo#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://ipernity.com/doc/'.$username.'/home/photo';
	}
	
	public function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        if(preg_match('/<img width="[0-9]+" height="[0-9]+" src="(.*)l\.jpg" /iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'t.jpg" /></a>';
        }
        return '';
	}
	
	public function getFeedUrl($username) {
		return 'http://www.ipernity.com/feed/'.$username.'/photocast/stream/rss.xml?key=';
	}
}