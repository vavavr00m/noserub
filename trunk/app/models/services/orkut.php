<?php
class OrkutService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#orkut.com/Profile.aspx\?uid=(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.orkut.com/Profile.aspx?uid='.$username;
	}
}