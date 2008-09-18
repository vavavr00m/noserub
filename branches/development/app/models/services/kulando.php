<?php
class KulandoService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).kulando.de#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.kulando.de';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://' . $username . '.kulando.de', '/view his <a href="\/people\/(.*)\/">profile<\/a>/iU', '/class="Next">Next &gt;<\/a>/iU', '?page=');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		# we need to read the page first in order to access
        # the user id without need to access the API
        $content = WebExtractor::fetchUrl('http://'.$username.'.kulando.de/');
        if(preg_match('/href="http:\/\/www.kulando.de\/rss.php\?blogId=(.*)&amp;profile=/i', $content, $matches)) {
        	return 'http://www.kulando.de/rss.php?blogId='.$matches[1].'&amp;profile=rss20';
        } else {
        	return false;
        }
	}
}