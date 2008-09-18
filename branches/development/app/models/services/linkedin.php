<?php
class LinkedinService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#www.linkedin.com/in/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.linkedin.com/in/'.$username;
	}
}