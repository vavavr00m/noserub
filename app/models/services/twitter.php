<?php
class TwitterService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#twitter.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://twitter.com/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://twitter.com/' . $username . '/', '/<a href="http:\/\/twitter\.com\/(.*)" class="url" rel="contact"/i');
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