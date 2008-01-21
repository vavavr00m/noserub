<?php
class IpernityService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#ipernity.com/doc/(.+)/home/photo#'));
	}
	
	function getAccountUrl($username) {
		return 'http://ipernity.com/doc/'.$username.'/home/photo';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://ipernity.com/user/' . $username . '/network', '/<a href="\/user\/(.*)">Profile<\/a>/iU', '/>next &rarr;<\/a>/iU', '|R58%3Bord%3D3%3Boff%3D0?r[off]=');
	}
	
	function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        if(preg_match('/<img width="[0-9]+" height="[0-9]+" src="(.*)l\.jpg" /iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'t.jpg" /></a>';
        }
        return '';
	}
	
	function getFeedUrl($username) {
		return 'http://www.ipernity.com/feed/'.$username.'/photocast/stream/rss.xml?key=';
	}
}
?>