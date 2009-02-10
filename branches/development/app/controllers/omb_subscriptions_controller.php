<?php

App::import('Vendor', array('OauthConstants', 'OmbAuthorizationParams', 'OmbAuthorizationResponse', 
							'OmbConstants', 'UrlUtil'));

class OmbSubscriptionsController extends AppController {
	const ACCESS_TOKEN_URL_KEY = 'omb.accessTokenUrl';
	const REQUEST_TOKEN_KEY = 'omb.requestToken';
	const LOCAL_SERVICE_ID_KEY = 'omb.localServiceId';
	public $uses = array('Identity', 'OmbLocalService');
	public $helpers = array('flashmessage');
	public $components = array('OmbRemoteService');
	
	public function callback() {
		$username = $this->getUsernameOrRedirect();
		
		try {
			$response = new OmbAuthorizationResponse($this->params['url']);
			$identity = $this->Identity->getIdentityByUsername($username);

			$data['Identity']['network_id'] = 0;
			$data['Identity']['username']   = UrlUtil::removeHttpAndHttps($response->getProfileUrl());
			
			$existingIdentityId = $this->Identity->field('id', array('Identity.username' => $data['Identity']['username']));
			if ($existingIdentityId) {
				$this->Identity->id = $existingIdentityId;
			}
			
			$this->Identity->save($data, true, array('network_id', 'username'));
			$this->Identity->Contact->add($identity['Identity']['id'], $this->Identity->id);
			$contactId = $this->Identity->Contact->id;
		
			if ($response->getAvatarUrl() != '') {
				$this->Identity->uploadPhotoByUrl($response->getAvatarUrl());
			}
			
			$accessTokenUrl = $this->Session->read(self::ACCESS_TOKEN_URL_KEY);
			$requestToken = $this->Session->read(self::REQUEST_TOKEN_KEY);
			$localServiceId = $this->Session->read(self::LOCAL_SERVICE_ID_KEY);
			$accessToken = $this->OmbRemoteService->getAccessToken($accessTokenUrl, $requestToken);				
			
			$this->OmbLocalService->OmbLocalServiceAccessToken->add($contactId, $localServiceId, $accessToken);
			
			$this->Session->delete(self::ACCESS_TOKEN_URL_KEY);
			$this->Session->delete(self::REQUEST_TOKEN_KEY);
			$this->Session->delete(self::LOCAL_SERVICE_ID_KEY);
			
			$this->flashMessage('Success', __('Successfully subscribed to ', true) . $username);
		} catch (InvalidArgumentException $e) {
			$this->flashMessage('Error', __('Invalid request', true));
		}
		
		$this->redirect('/'.$username);
	}
	
	public function subscribe() {
		$username = $this->getUsernameOrRedirect();
		$this->set('headline', __('Subscribe to ', true) . $username);
		
		if ($this->data) {
			try {
				$localServiceDefinition = $this->OmbRemoteService->discoverLocalService($this->data['Omb']['url']);
				$localServiceId = $this->OmbLocalService->getServiceId($localServiceDefinition);

				if (!$localServiceId) {
					$localServiceId = $this->OmbLocalService->add($localServiceDefinition);
				}

				$requestToken = $this->OmbRemoteService->getRequestToken($localServiceDefinition->getRequestTokenUrl(), $localServiceDefinition->getLocalId());
				
				$this->Session->write(self::REQUEST_TOKEN_KEY, $requestToken);
				$this->Session->write(self::ACCESS_TOKEN_URL_KEY, $localServiceDefinition->getAccessTokenUrl());
				$this->Session->write(self::LOCAL_SERVICE_ID_KEY, $localServiceId);
				
				$callbackUrl = Configure::read('NoseRub.full_base_url') . $username . '/callback';
				$identity = $this->Identity->getIdentityByUsername($username);
				$ombAuthorizationParams = new OmbAuthorizationParams($localServiceDefinition->getLocalId(), $identity);
				$this->OmbRemoteService->redirectToAuthorizationPage($localServiceDefinition->getAuthorizeUrl(), $requestToken, $ombAuthorizationParams, $callbackUrl);
			} catch (Exception $e) {
				$this->flashMessage('Error', $e->getMessage());
			}
		}
	}
	
	private function getUsernameOrRedirect() {
		App::import('Vendor', 'UsernameUtil');
		$username = isset($this->params['username']) ? $this->params['username'] : '';
		
		if ($username === '' || UsernameUtil::isReservedUsername($username)) {
			$this->redirect('/');
		}
		
		return $username;
	}
}