<?php
class SecondlifeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^#(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return '#'.$username;
	}	
}