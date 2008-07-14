<?php
class DopplrService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#dopplr.com/traveller/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.dopplr.com/traveller/'.$username;
	}
}
?>