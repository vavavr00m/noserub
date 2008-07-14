<?php
class IcqService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#icq.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.icq.com/'.$username;
	}
}
?>