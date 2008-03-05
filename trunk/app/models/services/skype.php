<?php
class SkypeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^skype:(.+)/'));
	}
	
	function getAccountUrl($username) {
		return 'skype:'.$username;
	}
}
?>