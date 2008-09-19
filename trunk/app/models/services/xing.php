<?php
class XingService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#xing.com/profile/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'https://www.xing.com/profile/'.$username;
	}
}