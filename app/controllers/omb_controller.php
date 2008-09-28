<?php

class OmbController extends AppController {
	public $uses = array('OmbAccessToken', 'OmbService');
	public $helpers = array('flashmessage', 'form');
	public $components = array('OmbConsumer');
	
	public function index() {
		$this->set('headline', 'OpenMicroBlogging');
		$session_identity = $this->Session->read('Identity');

		if ($this->data) {
			$endPoints = $this->OmbConsumer->discover($this->data['Omb']['url']);
			$id = $this->OmbService->add($endPoints);
			$this->Session->write('omb.service_id', $id);
			$this->OmbConsumer->redirectToAuthorizePage($endPoints, $session_identity);
		}
	}
	
	public function callback() {
		$session_identity = $this->Session->read('Identity');
		$service_id = $this->Session->read('omb.service_id');
		$accessToken = $this->OmbConsumer->getAccessToken();
		$this->OmbAccessToken->add($session_identity['id'], $service_id, $accessToken);
		
		$this->flashMessage('Success', 'Success!');;
		$this->redirect('/settings/omb');
	}
}