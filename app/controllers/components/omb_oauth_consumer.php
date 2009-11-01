<?php
App::import('Component', 'OauthConsumer');
App::import('Vendor', 'OmbConstants');

class OmbOauthConsumerComponent extends OauthConsumerComponent {
	protected function createOauthToken($response) {
		if (!$this->isCorrectOMBVersion($response)) {
			throw new InvalidArgumentException(__('Invalid OMB version', true));
		}
		
		if (isset($response['oauth_token']) && isset($response['oauth_token_secret'])) {
			return new OAuthToken($response['oauth_token'], $response['oauth_token_secret']);
		}
		
		return null;
	}
	
	// XXX due to bug http://laconi.ca/trac/ticket/681 we have to allow tokens 
	// without omb_version
	private function isCorrectOMBVersion($response) {
		if (isset($response['omb_version'])) {
			if ($response['omb_version'] !== OmbConstants::VERSION) {
				return false;
			}
		}
		
		return true;
	}
}