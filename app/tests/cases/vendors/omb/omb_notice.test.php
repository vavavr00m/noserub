<?php
App::import('Vendor', 'OmbNotice');

class OmbNoticeTest extends CakeTestCase {
	private $noticeId = 12;
	private $notice = 'This is a notice';
	private $ombNotice = null;
	
	public function setUp() {
		$this->ombNotice = new OmbNotice($this->noticeId, $this->notice);
	}
	
	public function testConstruction() {
		$this->assertEqual($this->noticeId, $this->ombNotice->getNoticeId());
		$this->assertEqual($this->notice, $this->ombNotice->getNotice());
	}
}