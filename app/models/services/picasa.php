<?php
class PicasaService extends AbstractService {
	
	public function init() {
	    $this->name = 'Picasa';
        $this->url = 'http://picasaweb.google.com/home';
        $this->service_type_id = 1;
        $this->icon = 'picasa.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#picasaweb.google.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://picasaweb.google.com/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://picasaweb.google.com/data/feed/base/user/' . $username . '?alt=rss&kind=photo&hl=en_US&access=public';
	}
}