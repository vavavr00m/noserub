<?php

class OmbNotice {
	private $noticeId = null;
	private $notice = null;
	
	public function __construct($noticeId, $notice) {
		$this->noticeId = $noticeId;
		$this->notice = $notice;
	}
	
	public function getNotice() {
		return $this->notice;
	}
	
	public function getNoticeId() {
		return $this->noticeId;
	}
}