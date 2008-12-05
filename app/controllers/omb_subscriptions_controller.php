<?php

App::import('Vendor', array('OauthConstants', 'OmbConstants', 'UrlUtil'));

class OmbSubscriptionsController extends AppController {
	public $uses = array('Identity', 'OmbServiceAccessToken', 'OmbService');
	public $helpers = array('flashmessage');
	public $components = array('OmbConsumer');
	
	public function callback() {
		$username = $this->getUsernameOrRedirect();
		
		try {
			$response = new OmbAuthorizationResponse($this->params['url']);
			$identity = $this->getIdentity($username);

			$data['Identity']['is_local'] = false;
			$data['Identity']['username'] = UrlUtil::removeHttpAndHttps($response->getProfileUrl());
			
			$existingId = $this->Identity->field('id', array('Identity.username' => $data['Identity']['username']));
			if ($existingId) {
				$data['Identity']['id'] = $existingId;
			}
			
			$this->Identity->save($data, true, array('is_local', 'username'));
			
			$this->Identity->Contact->add($identity['Identity']['id'], $this->Identity->id);
		
			$accessTokenUrl = $this->Session->read('omb.accessTokenUrl');
			$requestToken = $this->Session->read('omb.requestToken');
			$serviceId = $this->Session->read('omb.serviceId');
			$accessToken = $this->OmbConsumer->getAccessToken($accessTokenUrl, $requestToken);				
			
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
				$endPoint = $this->OmbConsumer->discover($this->data['Omb']['url']);
				$serviceId = $this->OmbService->getServiceId($endPoint->getPostNoticeUrl(), $endPoint->getUpdateProfileUrl());

				if (!$serviceId) {
					$serviceId = $this->OmbService->add($endPoint->getPostNoticeUrl(), $endPoint->getUpdateProfileUrl());
				}

				$requestToken = $this->OmbConsumer->getRequestToken($endPoint->getRequestTokenUrl(), $endPoint->getLocalId());
				
				$this->Session->write('omb.requestToken', $requestToken);
				$this->Session->write('omb.accessTokenUrl', $endPoint->getAccessTokenUrl());
				$this->Session->write('omb.serviceId', $serviceId);
				
				$identity = $this->getIdentity($username);
				$ombAuthorizationParams = new OmbAuthorizationParams($endPoint->getLocalId(), $identity);
				$this->redirect($this->OmbConsumer->constructAuthorizeUrl($endPoint->getAuthorizeUrl(), $requestToken, $ombAuthorizationParams));
			} catch (Exception $e) {
				$this->flashMessage('Error', $e->getMessage());
			}
		}
	}
	
	private function getIdentity($username) {
		$splitted = $this->Identity->splitUsername($username);
		$this->Identity->contain();
		$identity = $this->Identity->findByUsername($splitted['username']);
		
        return $identity;
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