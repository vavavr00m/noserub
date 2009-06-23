<?php
/* SVN FILE: $Id:$ */
 
class AccountsController extends AppController {
    public $uses = array('Account');
    public $helpers = array('flashmessage');
    
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
    		    $serviceData = $this->Account->Service->detectService($this->data['Account']['url']);
		    } else {
		        $serviceData = array(
		            'service_id' => $this->data['Account']['service_id'],
		            'username'   => $this->data['Account']['username']
		        );
		    }
		    
		    if($serviceData) {
	    		$info = $this->Account->Service->getInfoFromService(
	    		    Context::read('logged_in_identity.username'), 
	    		    $serviceData['service_id'], 
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
    			$info = $this->Account->Service->getInfoFromFeed(
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
                
				if(isset($this->data['Account']['service_type_id'])) {
					$info['service_type_id'] = $this->data['Account']['service_type_id'];
				}
				
				$saveable = array(
				    'identity_id', 'service_id', 'service_type_id', 
                    'username', 'account_url', 'feed_url', 'created', 
                    'modified', 'title'
                );
                $this->Account->create();
                $this->Account->save($info, true, $saveable);
                
                if($this->Account->id) {
                    // save feed information to entry table
                    $this->Account->Entry->updateByAccountId($this->Account->id);
                }
                
                $this->Account->Entry->addNewService(
                    $info['identity_id'], 
                    $info['service_id'], 
                    null
                );
    		}
        }
        
        $this->redirect($this->referer());
    }
    
    public function edit() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPut()) {
            $this->ensureSecurityToken();
            
            $account = $this->Account->find(
                'first',
                array(
                    'contain' => 'Service',
                    'conditions' => array(
                        'Account.id' => $this->data['Account']['id'],
                        'Account.identity_id' => Context::loggedInIdentityId()
                    )
                )
            );
            if($account) {
                if($account['Service']['is_contact']) {
                    $saveable = array(
                        'username', 'title', 'account_url'
                    );
                } else {
                    $saveable = array(
                        'service_type_id', 'username', 'title',
                        'account_url', 'feed_url'
                    );
                }
                
                $this->Account->id = $account['Account']['id'];
                $this->Account->save($this->data, true, $saveable);
                
                $this->redirect('/settings/accounts/');
            }
        }
    }
    
    public function delete() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPut()) {
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
                $this->Account->id = $this->data['Account']['id'];
                $this->Account->deleteWithAssociated();
                $this->redirect('/settings/accounts/');
            }
        } 
    }    
}
