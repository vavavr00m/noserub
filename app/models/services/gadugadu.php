<?php
class GadugaduService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^gg:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'gg:'.$username;
	}
}