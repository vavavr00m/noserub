<?php

class OauthController extends AppController {
	public $uses = array('OauthConsumer');
	public $helpers = array('flashmessage', 'form');
	private $session_identity = null;
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->OauthConsumer->Identity->splitUsername($username);
        $this->session_identity = $this->Session->read('Identity');
        
        if(!$this->session_identity || $this->session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
	}
	
	public function index() {
		$this->OauthConsumer->expects('OauthConsumer');
		$this->set('consumers', $this->OauthConsumer->findAllByIdentityId($this->session_identity['id']));
		$this->set('session_identity', $this->session_identity);
		$this->set('headline', 'OAuth');
	}
	
	public function add() {
		if ($this->data) {
			if ($this->data['OauthConsumer']['application_name']) {
				if ($this->OauthConsumer->add($this->session_identity['id'], $this->data['OauthConsumer']['application_name'])) {
					$this->flashMessage('success', 'Application registered.');
					$this->redirect($this->getOAuthSettingsUrl());
				} else {
					$this->flashMessage('error', 'Application could not be registered.');
				}
			} else {
				$this->OauthConsumer->invalidate('application_name');
			}
		}
		
		$this->set('session_identity', $this->session_identity);
		$this->set('headline', 'Register new application');
	}
	
	public function edit() {
		$consumer_id = isset($this->params['oauth_consumer_id']) ? $this->params['oauth_consumer_id'] : false;
		
		if (!$consumer_id) {
            $this->redirect($this->url->http('/'));
		}
		
		$this->OauthConsumer->expects('OauthConsumer');
		$consumer = $this->OauthConsumer->find(array('id' => $consumer_id, 'identity_id' => $this->session_identity['id']));
		
		if (!$consumer) {
			$this->flashMessage('error', 'Application could not be edited.');
			$this->redirect($this->getOAuthSettingsUrl());
		}
		
		if ($this->data) {
			if ($this->data['OauthConsumer']['application_name']) {
				$this->OauthConsumer->id = $consumer['OauthConsumer']['id'];
				if ($this->OauthConsumer->saveField('application_name', $this->data['OauthConsumer']['application_name'])) {
                    $this->flashMessage('success', 'Application updated.');
                } else {
                    $this->flashMessage('error', 'Application could not be updated.');
                }
                $this->redirect($this->getOAuthSettingsUrl());
			} else {
				$this->OauthConsumer->invalidate('application_name');
			}
		} else {
			$this->data = $consumer;
		}
		
		$this->set('session_identity', $this->session_identity);
		$this->set('headline', 'Edit application');
        $this->render('add');
	}
	
	public function delete() {
		$consumer_id = isset($this->params['oauth_consumer_id']) ? $this->params['oauth_consumer_id'] : false;
		
		if (!$consumer_id) {
            $this->redirect($this->url->http('/'));
		}
		
		$this->ensureSecurityToken();
		
		$this->OauthConsumer->expects('OauthConsumer');
		if (1 == $this->OauthConsumer->findCount(array('id' => $consumer_id, 'identity_id' => $this->session_identity['id']))) {
            $this->OauthConsumer->delete($consumer_id);
            
            $this->flashMessage('success', 'Application deleted.');            
        } else {
            $this->flashMessage('error', 'Application could not be deleted.');
        }
        
    	$this->redirect($this->getOAuthSettingsUrl());
	}
	
	private function getOAuthSettingsUrl() {
		return '/'.$this->session_identity['local_username'].'/settings/oauth';
	}
}
?>