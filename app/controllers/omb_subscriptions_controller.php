<?php

App::import('Vendor', 'OmbConstants');
App::import('Vendor', 'OauthConstants');

class OmbSubscriptionsController extends AppController {
	public $uses = array('Identity', 'OmbServiceAccessToken', 'OmbService');
	public $helpers = array('flashmessage', 'form');
	public $components = array('OmbConsumer');
	
	public function callback() {
		$username = isset($this->params['username']) ? $this->params['username'] : '';
		
		if (isset($this->params['url']['omb_version'])) {
			if ($this->params['url']['omb_version'] == OmbConstants::VERSION) {
				$identity = $this->getIdentity($username);

				$data['Identity']['is_local'] = false;
				$data['Identity']['username'] = str_replace('http://', '', $this->params['url']['omb_listener_profile']);
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
				
				$this->flashMessage('Success', 'Successfully subscribed to '.$username);
			} else {
				$this->flashMessage('Error', 'Invalid omb version');
			}
		} else {
			$this->flashMessage('Error', 'Invalid request');
		}
		
		$this->redirect('/'.$username);
	}
	
	public function subscribe() {
		$username = isset($this->params['username']) ? $this->params['username'] : '';
		$this->set('headline', 'Subscribe to '.$username);
		
		if ($this->data) {
			try {
				$endPoints = $this->OmbConsumer->discover($this->data['Omb']['url']);
				$localId = $endPoints[0];
				$requestTokenUrl = $endPoints[1][OauthConstants::REQUEST];
				$authorizeUrl = $endPoints[1][OauthConstants::AUTHORIZE];
				$accessTokenUrl = $endPoints[1][OauthConstants::ACCESS];
				$postNoticeUrl = $endPoints[1][OmbConstants::POST_NOTICE];
				$updateProfileUrl = $endPoints[1][OmbConstants::UPDATE_PROFILE];
				
				$serviceId = $this->OmbService->getServiceId($postNoticeUrl, $updateProfileUrl);

				if (!$serviceId) {
					$serviceId = $this->OmbService->add($postNoticeUrl, $updateProfileUrl);
				}

				$requestToken = $this->OmbConsumer->getRequestToken($requestTokenUrl, $localId);
				
				$this->Session->write('omb.requestToken', $requestToken);
				$this->Session->write('omb.accessTokenUrl', $accessTokenUrl);
				$this->Session->write('omb.serviceId', $serviceId);
				
				$identity = $this->getIdentity($username);
				$this->redirect($this->OmbConsumer->constructAuthorizeUrl($authorizeUrl, $localId, $requestToken, $identity));

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
}