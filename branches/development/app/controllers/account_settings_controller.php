<?php

class AccountSettingsController extends AppController {
	public $uses = array('Identity');
	public $components = array('url');
	public $helpers = array('flashmessage');
	private $session_identity = null;
	private $username = null;

	public function beforeFilter() {
		parent::beforeFilter();

		$this->username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($this->username);
        $this->session_identity = $this->Session->read('Identity');
        
        if(!$this->session_identity || $this->session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
	}
	
	public function index() {
        $this->checkSecure();

        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
			$this->deleteAccount($this->session_identity, $this->data['Identity']['confirm']);
        } else {
            $this->Identity->id = $this->session_identity['id'];
            $this->Identity->contain();
            $this->data = $this->Identity->read();
        }
        
        $this->set('headline', __('Manage your account', true));
    }
    
	public function export() {
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
            
        $this->id = $this->session_identity['id'];
		$data = $this->Identity->export();
		$this->set('data', $data);
        $this->layout = 'empty';
    }
    
	public function import() {
        $this->Session->delete('Import.data');
        
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();

            if($this->data['Import']['data']['error']) {
                $this->flashMessage('alert', __('There was an error while uploading', true));
            } else {
                $filename = $this->data['Import']['data']['tmp_name'];
                $data = $this->Identity->readImport($filename);
                if($data) {
                    $this->Session->write('Import.data', $data);
                    $this->set('data', $data);
                    $this->set('headline', __('Importing NoseRub data', true));
                    $this->render();
                    return;
                } else {
                    $this->flashMessage('alert', __("Couldn't import the data!", true));
                }
            }
        } 
            
        $this->redirect('/' . $this->username . '/settings/account/');
    }
    
	public function import_data() {
        $data = $this->Session->read('Import.data');
        if(!$data) {
            $this->flashMessage('alert', __("Couldn't import the data!", true));
        } else {
            $this->Identity->id = $this->session_identity['id'];
            if($this->Identity->import($this->context, $data)) {
                $this->flashMessage('success', __('Import completed', true));
            } else {
                $this->flashMessage('alert', __('There was an error during import!', true));
            }
        }
            
        $this->redirect('/' . $this->username . '/settings/account/');
    }
    
	public function redirect_url() {
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
            $redirect_url = $this->data['Identity']['redirect_url'];

            if($redirect_url && !UrlUtil::startsWithHttpOrHttps($redirect_url)) {
                $redirect_url = 'http://' . $redirect_url;
            }
            $this->Identity->id = $this->session_identity['id'];
            $this->Identity->saveField('redirect_url', $redirect_url);
            $this->flashMessage('success', __('Redirect URL saved.', true));
        }
        
        $this->redirect('/' . $this->username . '/settings/account/');
    }
    
	private function deleteAccount($identity, $confirm) {
    	if($confirm == 0) {
			$this->set('confirm_error', __('In order to delete your account, please check the check box.', true));
		} else if($confirm == 1) {
			$identityId = $identity['id'];
			$this->Identity->Account->deleteByIdentityId($identityId);
			$this->Identity->Contact->deleteByIdentityId($identityId, $identity['local_username']);
			$this->Identity->block($identityId);
			$this->Session->delete('Identity');
			$this->redirect('/pages/account/deleted/');
		}
    }
}