<?php
class WikipediaService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#en.wikipedia.org/wiki/User:(.+)#'));
	}
	
	
	public function getAccountUrl($username) {
		return 'http://en.wikipedia.org/wiki/User:'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://en.wikipedia.org/w/index.php?title=Special:Contributions&feed=rss&target='.$username;
	}
}