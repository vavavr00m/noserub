<?php
class TwitterService extends AbstractService {
	
	public function init() {
	    $this->name = 'Twitter';
        $this->url = 'http://twitter.com/';
        $this->service_type_id = 5;
        $this->icon = 'twitter.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#twitter.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://twitter.com/'.$username;
	}
	
	public function getTitle($feeditem) {
	    return $this->getContent($feeditem);
	}
	
	public function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_content();
        return substr($content, strpos($content, ': ') + 2);
	}
	
	public function getFeedUrl($username) {
		# we need to reed the page first in order to
        # access the rss-feed
        App::import('Vendor', 'WebExtractor');
        $content = WebExtractor::fetchUrl('http://twitter.com/'.$username);
        if(!$content) {
        	return false;
        }
        if(preg_match('/http:\/\/twitter\.com\/statuses\/user_timeline\/([0-9]*)\.rss/i', $content, $matches)) {
        	return 'http://twitter.com/statuses/user_timeline/'.$matches[1].'.rss';
        } else {
        	return false;
        }
	}
}