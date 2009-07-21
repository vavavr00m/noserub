<?php
/* SVN FILE: $Id:$ */
 
class AccountsController extends AppController {
    public $uses = array('Account');
    
    public function settings() {
        $this->grantAccess('self');
    }
    
    public function add() {
        $this->grantAccess('self');
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
                       
            $autodetect = false;
            if(isset($this->data['Account']['url'])) {
                $autodetect = true;
    		    $serviceData = $this->Service->detectService($this->data['Account']['url']);
		    } else {
		        $serviceData = array(
		            'service' => $this->data['Account']['service'],
		            'username' => $this->data['Account']['username']
		        );
		    }
		    
		    if($serviceData) {
	    		$info = $this->Service->getInfoFromService(
	    		    Context::read('logged_in_identity.username'),
	    		    $serviceData['service'],
	    		    $serviceData['username']
	    		);    

	    		if(!$info) {
	    		    if($autodetect) {
	                    $this->Account->invalidate('url', 1);
	                    $this->storeFormErrors('Account', $this->data, $this->Account->validationErrors);
                    } else {
                        $this->Account->invalidate('username', 1);
                        $this->storeFormErrors('Account', $this->data, $this->Account->validationErrors);
                    }
	            } 
    		} else {
    			// as we don't know the service type id yet we set the id to 3 for Text/Blog 
    			$info = $this->Service->getInfoFromFeed(
    			    Context::read('logged_in_identity.username'), 
    			    3, // Text/Blog
    			    $this->data['Account']['url']
    			);

    			if(!$info) {
    				$this->Account->invalidate('url', 1);
    				$this->storeFormErrors('Account', $this->data, $this->Account->validationErrors);
    			} 
    		}
    		
    		if($info) {
    		    $info['identity_id'] = Context::loggedInIdentityId();
				
                if(isset($this->data['Account']['label']) && !empty($this->data['Account']['label'])) {
					$info['title'] = $this->data['Account']['label'];
				}
                
				if(isset($this->data['Account']['service_type'])) {
					$info['service_type'] = $this->data['Account']['service_type'];
				}
				
				$saveable = array(
				    'identity_id', 'service', 'service_type', 
                    'username', 'account_url', 'feed_url', 'created', 
                    'modified', 'title'
                );
                $this->Account->create();
                $this->Account->save($info, true, $saveable);
                $this->flashMessage('success', __('Account has been created', true));
                
                if($this->Account->id) {
                    // save feed information to entry table
                    $this->Account->Entry->updateByAccountId($this->Account->id);
                }

                $this->Account->Entry->addNewService(
                    $info['identity_id'], 
                    $info['service'], 
                    null
                );
    		}
        }
        
        $this->redirect($this->referer());
    }
    
    public function edit() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
            
            $account = $this->Account->find(
                'first',
                array(
                    'conditions' => array(
                        'Account.id' => $this->data['Account']['id'],
                        'Account.identity_id' => Context::loggedInIdentityId()
                    )
                )
            );
            if($account) {
                $services = Configure::read('services.data');
                
                if($services[$account['Account']['service']]['is_contact']) {
                    $saveable = array(
                        'username', 'title', 'account_url'
                    );
                } else {
                    $saveable = array(
                        'service_type', 'username', 'title',
                        'account_url', 'feed_url'
                    );
                }
                
                if($account['Account']['service'] == 'RSS-Feed') {
                    // the username is just a dummy for generic RSS-Feeds
                    unset($saveable['username']);
                }
                
                $this->Account->id = $account['Account']['id'];
                $this->Account->save($this->data, true, $saveable);
                $this->flashMessage('success', __('Changes have been saved', true));
                
                $this->redirect('/settings/accounts/');
            }
        }
    }
    
    public function delete() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
            
            $account = $this->Account->find(
                'first',
                array(
                    'conditions' => array(
                        'Account.id' => $this->data['Account']['id'],
                        'Account.identity_id' => Context::loggedInIdentityId()
                    )
                )
            );
            if($account) {
                // delete all entries, comments and favorites
                $this->Account->Entry->deleteByAccountId($this->data['Account']['id']);
                // delete the account itself
                $this->Account->id = $this->data['Account']['id'];
                $this->Account->delete();
                $this->flashMessage('success', __('Account has been deleted', true));
                $this->redirect('/settings/accounts/');
            }
        } 
    }    
}
