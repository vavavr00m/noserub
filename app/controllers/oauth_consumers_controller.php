<?php

class OauthConsumersController extends AppController {
	public $uses = array('Consumer');
	public $components = array('url');
	public $helpers = array('flashmessage');
	private $session_identity = null;
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Consumer->Identity->splitUsername($username);
        $this->session_identity = $this->Session->read('Identity');
        
        if(!$this->session_identity || $this->session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
	}
	
	public function index() {
		$this->Consumer->contain();
		$this->set('consumers', $this->Consumer->findAllByIdentityId($this->session_identity['id']));
		$this->set('session_identity', $this->session_identity);
		$this->set('headline', 'OAuth');
	}
	
	public function add() {
		if ($this->data) {
			if ($this->data['Consumer']['application_name']) {
				if ($this->Consumer->add($this->session_identity['id'], $this->data['Consumer']['application_name'], $this->data['Consumer']['callback_url'])) {
					$this->flashMessage('success', __('Application registered.', true));
					$this->redirect($this->getOAuthSettingsUrl());
				} else {
					$this->flashMessage('error', __('Application could not be registered.', true));
				}
			} else {
				$this->Consumer->invalidate('application_name');
			}
		}
		
		$this->set('session_identity', $this->session_identity);
		$this->set('headline', __('Register new application', true));
	}
	
	public function edit() {
		$consumer_id = isset($this->params['consumer_id']) ? $this->params['consumer_id'] : false;
		
		if (!$consumer_id) {
            $this->redirect($this->url->http('/'));
		}
		
		$this->Consumer->contain();
		$consumer = $this->Consumer->find(array('id' => $consumer_id, 'identity_id' => $this->session_identity['id']));
		
		if (!$consumer) {
			$this->flashMessage('error', __('Application could not be edited.', true));
			$this->redirect($this->getOAuthSettingsUrl());
		}
		
		if ($this->data) {
			if ($this->data['Consumer']['application_name']) {
				$this->Consumer->id = $consumer['Consumer']['id'];
				if ($this->Consumer->saveField('application_name', $this->data['Consumer']['application_name'])) {
                    $this->flashMessage('success', __('Application updated.', true));
                } else {
                    $this->flashMessage('error', __('Application could not be updated.', true));
                }
                $this->redirect($this->getOAuthSettingsUrl());
			} else {
				$this->Consumer->invalidate('application_name');
			}
		} else {
			$this->data = $consumer;
		}
		
		$this->set('session_identity', $this->session_identity);
		$this->set('headline', __('Edit application', true));
        $this->render('add');
	}
	
	public function delete() {
		$consumer_id = isset($this->params['consumer_id']) ? $this->params['consumer_id'] : false;
		
		if (!$consumer_id) {
            $this->redirect($this->url->http('/'));
		}
		
		$this->ensureSecurityToken();
		
		if ($this->Consumer->hasAny(array('id' => $consumer_id, 'identity_id' => $this->session_identity['id']))) {
            $this->Consumer->delete($consumer_id);
            
            $this->flashMessage('success', __('Application deleted.', true));            
        } else {
            $this->flashMessage('error', __('Application could not be deleted.', true));
        }
        
    	$this->redirect($this->getOAuthSettingsUrl());
	}
	
	private function getOAuthSettingsUrl() {
		return '/'.$this->session_identity['local_username'].'/settings/oauth';
	}
}