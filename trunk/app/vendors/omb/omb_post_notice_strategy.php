<?php

class OmbPostNoticeStrategy {
	private $remoteService = null;
	private $accessTokens = null;
	private $notice = null;
	
	public function __construct(OmbRemoteServiceComponent $remoteService, array $accessTokens, OmbNotice $notice) {
		$this->remoteService = $remoteService; 
		$this->accessTokens = $accessTokens;
		$this->notice = $notice;
	}
	
	public function execute() {
		$alreadyUsedPostNoticeUrls = array();

		foreach ($this->accessTokens as $accessToken) {
			$postNoticeUrl = $accessToken['OmbLocalService']['post_notice_url'];
			if (!in_array($postNoticeUrl, $alreadyUsedPostNoticeUrls)) {
				$alreadyUsedPostNoticeUrls[] = $postNoticeUrl;
				$this->remoteService->postNoticeToUrl($accessToken['OmbLocalServiceAccessToken']['token_key'], 
												 	  $accessToken['OmbLocalServiceAccessToken']['token_secret'], 
												 	  $postNoticeUrl, 
													  $this->notice);
			}
		}
	}
}