<?php
class FlickrService extends AbstractService {
	
	public function init() {
	    $this->name = 'Flickr';
        $this->url = 'http://flickr.com/';
        $this->service_type_id = 1;
        $this->icon = 'flickr.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#flickr.com/photos/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.flickr.com/photos/'.$username.'/';
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
        App::import('Vendor', 'WebExtractor');
        $content = WebExtractor::fetchUrl('http://www.flickr.com/photos/'.$username.'/');
        if(preg_match('/photos_public.gne\?id=(.*)&amp;/i', $content, $matches)) {
        	return 'http://api.flickr.com/services/feeds/photos_public.gne?id='.$matches[1].'&lang=en-us&format=rss_200';
        } else {
        	return false;
        }
	}
}