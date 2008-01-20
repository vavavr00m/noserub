<?php
class DopplrService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#dopplr.com/traveller/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.dopplr.com/traveller/'.$username;
	}
}
?>