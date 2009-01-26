<?php

class OmbUpdateProfileStrategy {
	private $remoteService = null;
	private $accessTokens = null;
	private $data = null;
	
	public function __construct(OmbRemoteServiceComponent $remoteService, array $accessTokens, OmbUpdatedProfileData $data) {
		$this->remoteService = $remoteService; 
		$this->accessTokens = $accessTokens;
		$this->data = $data;
	}
	
	public function execute() {
		$alreadyUsedUpdateProfileUrls = array();

		foreach ($this->accessTokens as $accessToken) {
			$updateProfileUrl = $accessToken['OmbLocalService']['update_profile_url'];
			if (!in_array($updateProfileUrl, $alreadyUsedUpdateProfileUrls)) {
				$alreadyUsedUpdateProfileUrls[] = $updateProfileUrl;
				$this->remoteService->updateProfileToUrl($accessToken['OmbLocalServiceAccessToken']['token_key'], 
														 $accessToken['OmbLocalServiceAccessToken']['token_secret'], 
														 $updateProfileUrl, 
														 $this->data);
			}
		}
	}
}