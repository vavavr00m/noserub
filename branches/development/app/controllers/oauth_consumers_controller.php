<?php

class OauthConsumersController extends AppController {
	public $uses = array('Consumer');
	public $components = array('url');
	private $logged_in_identity = null;
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		if (!Context::isLoggedInIdentity()) {
			$this->redirect('/');
		}

        $this->logged_in_identity = Context::read('logged_in_identity');
	}
	
	public function index() {
		$this->Consumer->contain();
		$this->set('consumers', $this->Consumer->findAllByIdentityId($this->logged_in_identity['id']));
		$this->set('session_identity', $this->logged_in_identity);
		$this->set('headline', 'OAuth');
	}
	
	public function add() {
		if ($this->data) {
			if ($this->data['Consumer']['application_name']) {
				if ($this->Consumer->add($this->logged_in_identity['id'], $this->data['Consumer']['application_name'], $this->data['Consumer']['callback_url'])) {
					$this->flashMessage('success', __('Application registered.', true));
					$this->redirect($this->getOAuthSettingsUrl());
				} else {
					$this->flashMessage('error', __('Application could not be registered.', true));
				}
			} else {
				$this->Consumer->invalidate('application_name');
			}
		}
		
		$this->set('session_identity', $this->logged_in_identity);
		$this->set('headline', __('Register new application', true));
	}
	
	public function edit() {
		$consumer_id = isset($this->params['consumer_id']) ? $this->params['consumer_id'] : false;
		
		if (!$consumer_id) {
            $this->redirect($this->url->http('/'));
		}
		
		$this->Consumer->contain();
		$consumer = $this->Consumer->find(array('id' => $consumer_id, 'identity_id' => $this->logged_in_identity['id']));
		
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
		
		$this->set('session_identity', $this->logged_in_identity);
		$this->set('headline', __('Edit application', true));
        $this->render('add');
	}
	
	public function delete() {
		$consumer_id = isset($this->params['consumer_id']) ? $this->params['consumer_id'] : false;
		
		if (!$consumer_id) {
            $this->redirect($this->url->http('/'));
		}
		
		$this->ensureSecurityToken();
		
		if ($this->Consumer->hasAny(array('id' => $consumer_id, 'identity_id' => $this->logged_in_identity['id']))) {
            $this->Consumer->delete($consumer_id);
            
            $this->flashMessage('success', __('Application deleted.', true));            
        } else {
            $this->flashMessage('error', __('Application could not be deleted.', true));
        }
        
    	$this->redirect($this->getOAuthSettingsUrl());
	}
	
	private function getOAuthSettingsUrl() {
		return '/settings/oauth';
	}
}