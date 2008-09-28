<?php
class BlipprService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#blippr.com/profiles/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.blippr.com/profiles/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.blippr.com/profiles/' . $username . '/friends', '/<a href="http:\/\/www\/.blippr\.com\/profiles\/(.*)">.*<\/a>/');
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