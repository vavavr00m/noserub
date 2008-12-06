<?php
class PlazesService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#plazes.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://plazes.com/users/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		# We need to find the RSS feed as it is now tied to the user number not user ID
		$content = @file_get_contents('http://plazes.com/users/'.$username);
        if(!$content) {
        	return false;
        }
        if(preg_match('/http:\/\/plazes\.com\/users\/([0-9]*)\/activities\.atom/i', $content, $matches)) {
        	return 'http://plazes.com/users/'.$matches[1].'/activities.atom';
        } else {
        	return false;
        }
		
		# http://plazes.com/users/3465/activities.atom
	}
}