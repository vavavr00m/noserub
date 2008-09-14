<?php
class FlickrService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#flickr.com/photos/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.flickr.com/photos/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://www.flickr.com/people/' . $username . '/contacts/', '/view his <a href="\/people\/(.*)\/">profile<\/a>/iU', '/class="Next">Next &gt;<\/a>/iU', '?page=');
	}
	
	public function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        if(preg_match('/<a href="http:\/\/www.flickr.com\/photos\/.*<\/a>/iU', $raw_content, $matches)) {
            $content = str_replace('_m.jpg', '_s.jpg', $matches[0]);
            $content = preg_replace('/width="[0-9]+".+height="[0-9]+"/i', '', $content);
            return $content;
        }
        return '';
	}
	
	public function getFeedUrl($username) {
		# we need to read the page first in order to access
        # the user id without need to access the API
        $content = WebExtractor::fetchUrl('http://www.flickr.com/photos/'.$username.'/');
        if(preg_match('/photos_public.gne\?id=(.*)&amp;/i', $content, $matches)) {
        	return 'http://api.flickr.com/services/feeds/photos_public.gne?id='.$matches[1].'&lang=en-us&format=rss_200';
        } else {
        	return false;
        }
	}
}
?>