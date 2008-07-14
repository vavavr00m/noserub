<?php
class YimService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#edit.yahoo.com/config/send_webmesg\?.target=(.+)&.src=pg#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://edit.yahoo.com/config/send_webmesg?.target='.$username.'&.src=pg';
	}
}
?>