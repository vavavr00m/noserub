<?php
class WiiService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^wii:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'wii:' . $username;
	}
}