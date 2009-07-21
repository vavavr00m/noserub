<?php
class DisqusService extends AbstractService {
	
	public function init() {
	    $this->name = 'Disqus';
        $this->url = 'http://disqus.com/';
        $this->service_type = 3;
        $this->icon = 'disqus.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#disqus.com/people/(.+)/#'));
		#http://disqus.com/people/lancew
	}
	
	public function getAccountUrl($username) {
		return 'http://disqus.com/people/'.$username;
	}
	
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://disqus.com/people/'.$username.'/comments.rss';
		#http://disqus.com/people/lancew/comments.rss
	}
}