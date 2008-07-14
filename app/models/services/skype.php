<?php
class SkypeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^skype:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'skype:'.$username;
	}
}
?>