<?php

App::import('Vendor', array('OauthConstants', 'OmbConstants', 'UrlUtil'));

class OmbSubscriptionsController extends AppController {
	public $uses = array('Identity', 'OmbServiceAccessToken', 'OmbLocalService');
	public $helpers = array('flashmessage');
	public $components = array('OmbRemoteService');
	
	public function callback() {
		$username = $this->getUsernameOrRedirect();
		
		try {
			$response = new OmbAuthorizationResponse($this->params['url']);
			$identity = $this->Identity->getIdentityByUsername($username);

			$data['Identity']['is_local'] = false;
			$data['Identity']['username'] = UrlUtil::removeHttpAndHttps($response->getProfileUrl());
			
			$existingId = $this->Identity->field('id', array('Identity.username' => $data['Identity']['username']));
			if ($existingId) {
				$this->Identity->id = $existingId;
			}
			
			$this->Identity->save($data, true, array('is_local', 'username'));
			$this->Identity->Contact->add($identity['Identity']['id'], $this->Identity->id);
		
			if ($response->getAvatarUrl() != '') {
				$this->Identity->uploadPhotoByUrl($response->getAvatarUrl());
			}
			
			$accessTokenUrl = $this->Session->read('omb.accessTokenUrl');
			$requestToken = $this->Session->read('omb.requestToken');
			$serviceId = $this->Session->read('omb.serviceId');
			$accessToken = $this->OmbRemoteService->getAccessToken($accessTokenUrl, $requestToken);				
			
			$this->OmbServiceAccessToken->add($identity['Identity']['id'], $serviceId, $accessToken);
			
			$this->Session->delete('omb.accessTokenUrl');
			$this->Session->delete('omb.requestToken');
			$this->Session->delete('omb.serviceId');
			
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
				$serviceId = $this->OmbLocalService->getServiceId($localServiceDefinition);

				if (!$serviceId) {
					$serviceId = $this->OmbLocalService->add($localServiceDefinition);
				}

				$requestToken = $this->OmbRemoteService->getRequestToken($localServiceDefinition->getRequestTokenUrl(), $localServiceDefinition->getLocalId());
				
				$this->Session->write('omb.requestToken', $requestToken);
				$this->Session->write('omb.accessTokenUrl', $localServiceDefinition->getAccessTokenUrl());
				$this->Session->write('omb.serviceId', $serviceId);
				
				$identity = $this->Identity->getIdentityByUsername($username);
				$ombAuthorizationParams = new OmbAuthorizationParams($localServiceDefinition->getLocalId(), $identity);
				$this->OmbRemoteService->redirectToAuthorizationPage($localServiceDefinition->getAuthorizeUrl(), $requestToken, $ombAuthorizationParams);
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