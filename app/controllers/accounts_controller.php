<?php
/* SVN FILE: $Id:$ */
 
class AccountsController extends AppController {
    public $uses = array('Account');
    public $helpers = array('flashmessage');
    
    public function settings() {
        $this->grantAccess('self');
    }
    
    /**
     * This is going to be obsolete, once AccountsController::settings() is done.
     */
    public function index() {
        $this->checkSecure();
        $this->grantAccess('self');
        
        # get all accounts
		$this->Account->contain('Service');
		$data = $this->Account->findAllByIdentity_id($identity['Identity']['id']);
		usort($data, 'sort_accounts');
        $this->set('data', $data);
        $this->set('session_identity', $session_identity);
        
        if($session_identity['username'] == $splitted['username']) {
            $this->set('headline', __('Your accounts', true));
        } else {
            $this->set('headline', sprintf(__("%s's accounts", true), $splitted['local_username']));
        }
        
        $this->set('contact_accounts', $this->Account->Service->getContactAccounts());
        
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();

            if(isset($this->data['TwitterAccount']['username'])) {
                # settings for the Twitter bridge have been made
                $this->Account->Identity->TwitterAccount->update($session_identity['id'], $this->data);
                
                $need_redirect = true;
            } else {
                $need_redirect = false;
            
                # extract service_id and username from $data
                $present_contact_accounts = array();
                foreach($data as $item) {
                    if($item['Service']['is_contact']) {
                        $present_contact_accounts[$item['Service']['id']] = array(
                            'username'   => $item['Account']['username'],
                            'account_id' => $item['Account']['id']);
                    }
                }
                foreach($this->data['Service'] as $service_id => $item) {
                    # go through each given data and test, wether we already have
                    # account-data for it.
                    if(isset($present_contact_accounts[$service_id])) {
                        if($item['username'] == '') {
                            # account can be deleted
                            $account_id = $present_contact_accounts[$service_id]['account_id'];
                            $this->Account->id = $account_id;
                            $this->Account->delete();
                            $need_redirect = true;
                        } else if($present_contact_accounts[$service_id]['username'] != $item['username']) {
                            # username was changed!
                            $this->Account->id = $present_contact_accounts[$service_id]['account_id'];
                            $this->Account->saveField('username', $item['username']);
                            $need_redirect = true;
                        }
                    } else if($item['username']) {
                        # new account needs to be created
                        $new_account = array(
                            'identity_id' => $session_identity['id'],
                            'service_id'  => $service_id,
                            'service_type_id' => $this->Account->Service->getServiceTypeId($service_id),
                            'username'        => $item['username'],
                            'account_url'     => $this->Account->Service->getAccountUrl($service_id, $item['username']));
                        $this->Account->create();
                        $this->Account->save($new_account, true, array('identity_id', 'service_id', 'service_type_id', 'username', 'account_url', 'created', 'modified'));
                        $need_redirect = true;
                    }
                }
            }
            if($need_redirect) {
                # we need to redirect, because $data is already outdated 
                # after the changes we just made
                $this->flashMessage('success', __('Changes saved.', true));
                // $this->redirect($this->here); doesn't work if you install NoseRub in a subdirectory
                $this->header('Location: '.$this->here);
                exit;
            } else {
                $this->flashMessage('info', __('No changes made.', true));
            }
        } else {
            $this->data = $identity;
        }
    }
    
    public function add_step_1() {
    	$username = isset($this->params['username']) ? $this->params['username'] : '';
    	$session_identity = $this->Session->read('Identity');
    	$splitted = $this->Account->Identity->splitUsername($username);
    	
    	$this->Session->delete('Service.add.account.to.identity_id');
        $this->Session->delete('Service.add.id');
    	
    	# only logged in users can add accounts
        if(!$session_identity) {
            # this user is not logged in
            $this->flashMessage('error', __('You need to be logged in to add an account.', true));
            $this->redirect('/');
        }

        $identity = $this->getIdentity($splitted['username']);
        
        if($identity['Identity']['id'] != $session_identity['id']) {
            # identity is not the logged in user
            
            if(!$identity || $identity['Identity']['namespace'] != $session_identity['local_username']) {
                # Identity not found, or identity's namespace does not match logged in username
                $this->redirect('/');
            }
            
            $this->Session->write('Service.add.account.is_logged_in_user', true);
        }
        
        # save identity for which we want to add the servie
        # into session, so we don't need to check any further
        $this->Session->write('Service.add.account.to.identity_id', $identity['Identity']['id']);
        
        # also save, wether we add the account for a logged in user. this is
        # needed to distinguish during the process (eg no import of conacts)
		$this->Session->write('Service.add.account.is_logged_in_user', $identity['Identity']['id'] == $session_identity['id']);        
        
    	if($this->data) {
    		$this->ensureSecurityToken();
    		$autodetect = false;
            if($this->data['Account']['url']) {
                $autodetect = true;
    		    $serviceData = $this->Account->Service->detectService($this->data['Account']['url']);
		    } else {
		        $serviceData = array(
		            'service_id' => $this->data['Account']['service_id'],
		            'username'   => $this->data['Account']['username']
		        );
		    }
		    
    		if($serviceData) {
    			$this->Session->write('Service.add.id', $serviceData['service_id']);
	    		$data = $this->Account->Service->getInfoFromService($splitted['username'], $serviceData['service_id'], $serviceData['username']);    

	    		if(!$data) {
	    		    if($autodetect) {
	                    $this->Account->invalidate('url', 1);
                    } else {
                        $this->Account->invalidate('username', 1);
                    }
	            } else {
	            	$this->saveToSessionAndRedirectToPreview($data);
	            }
    		} else {
    			$this->Session->write('Service.add.id', 8); # any rss feed
    			
    			// as we don't know the service type id yet we set the id to 3 for Text/Blog 
    			$data = $this->Account->Service->getInfoFromFeed($splitted['username'], 3, $this->data['Account']['url']);

    			if (!$data) {
    				$this->Account->invalidate('url', 1);
    			} else {
    				$this->saveToSessionAndRedirectToPreview($data);
    			}
    		}
    	}
    	
    	$this->Account->Service->contain();
    	$this->set('services', $this->Account->Service->find('list', array(
    	    'conditions' => array(
    	        'is_contact' => '0',
    	        'service_type_id > 0'),
    	    'order' => 'name ASC')));
    	$this->set('headline', 'Specify the service url');
    }
    
    public function add_step_2_preview() {
        $identity_id      = $this->Session->read('Service.add.account.to.identity_id');
        $session_identity = $this->Session->read('Identity');
        $data             = $this->Session->read('Service.add.data');
        
        # check the session vars
        if(!$identity_id || !$data) {
            # couldn't find the session vars. so either someone skipped 
            # a step, or the user was logged out during the process
            $this->redirect('/');
        }

        $data['service_id'] = $this->Session->read('Service.add.id');
        if(!empty($this->params['form'])) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
            # reset session
            $this->Session->delete('Service.add.data');

            if(isset($this->params['form']['submit'])) {
                # check if the acccount is not already there
                if(!$this->Account->hasAny(array('identity_id' => $identity_id, 'account_url' => $data['account_url']))) {
                    # save the new account
                    $data['identity_id'] = $identity_id;
					
                    if (isset($this->data['Account']['title']) && !empty($this->data['Account']['title'])) {
						$data['title'] = $this->data['Account']['title'];
					}
                    
					if (isset($this->data['Account']['service_type_id'])) {
						$data['service_type_id'] = $this->data['Account']['service_type_id'];
					}
					
					$saveable = array('identity_id', 'service_id', 'service_type_id', 
                                      'username', 'account_url', 'feed_url', 'created', 
                                      'modified', 'title');
                    $this->Account->create();
                    $this->Account->save($data, true, $saveable);

                    if($this->Account->id) {
                        # save feed information to entry table
                        $this->Account->Entry->updateByAccountId($this->Account->id);
                        # update service type id
                        $this->Account->saveField('service_type_id', $data['service_type_id']);
                    }
                    
                    $this->Account->Entry->addNewService($identity_id, $data['service_id'], null);
                    
                    $this->flashMessage('success', __('Account added.', true));
                }
            }
            # we're done!
            if($identity_id == $session_identity['id']) {
                # new account for the logged in user, so we redirect to his/her account settings
                $this->redirect('/settings/accounts/');
            } else {
                # new account for a private contact. redirect to his/her profile
                $this->Account->Identity->contain();
                $account_for_identity = $this->Account->Identity->findById($identity_id);
                $this->redirect('/' . $account_for_identity['Identity']['local_username'] . '/');
            }
        }
        // for feeds it must be possible to select the service type
        if ($data['service_id'] == 8) {
        	$this->set('service_types', $this->Account->ServiceType->find('list'));
        }
        $this->set('data', $data);
        $this->set('headline', __('Preview the data', true));
    }
    
    public function edit($account_id) {
        $session_identity = $this->Session->read('Identity');
        $identity_id      = $session_identity['id'];

        # check the session vars
        if(!$session_identity) {
            $this->redirect('/');
        }

        # get the account
        $this->Account->contain();
        $data = $this->Account->find(array('id' => $account_id, 'identity_id' => $identity_id));
        if(!$data) {
            # the account for this identity could not be found
            $this->redirect('/');
        }
        
        if(!$this->data) {
            $this->set('headline', sprintf('Edit service "%s"', htmlentities($data['Account']['title'], ENT_QUOTES, 'UTF-8')));
            $this->set('service_types', $this->Account->ServiceType->find('list'));
            $this->data = $data;
        } else {
            $this->Account->id = $account_id;
            if($this->Account->save($this->data, true, array('title', 'service_type_id'))) {
                $this->Account->Feed->updateServiceType($this->Account->id, $this->data['Account']['service_type_id']);
                $this->redirect('/settings/accounts/');
            }
        }
        
        $this->render('edit');
    }
    
    public function delete() {
        $account_id       = isset($this->params['account_id']) ? $this->params['account_id'] : '';
        $username         = isset($this->params['username'])   ? $this->params['username']   : '';
        $splitted         = $this->Account->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        # check the session vars
        if(!$username || !$session_identity) {
            # this user is not logged in
            $this->redirect('/');
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();

        if($splitted['username'] != $session_identity['username']) {
            # check, if $username belongs to the
            # logged in identities namespace
            $about_identity = $this->getIdentity($splitted['username']);
            if(!$about_identity) {
                # could not find the identity
                $this->flashMessage('alert', __('Could not find the user.', true));
                $this->redirect('/' . $splitted['local_username'] . '/');
            }
            if($about_identity['Identity']['namespace'] == $session_identity['local_username']) {
                $identity_id = $about_identity['Identity']['id'];
            } else {
                # this logged in user is not allowed to change something
                $this->flashMessage('alert', __('You may not delete this.', true));
                $this->redirect('/' . $splitted['local_username'] . '/');
            }
        }
        # check, wether the account belongs to the identity
        if ($this->Account->hasAny(array('identity_id' => isset($identity_id) ? $identity_id : $session_identity['id'],
                                                'id'          => $account_id))) {
            $this->Account->id = $account_id;
            $this->Account->delete();
            $this->Account->query('DELETE FROM ' . $this->Account->tablePrefix . 'entries WHERE account_id=' . $account_id);
            $this->flashMessage('success', __('Account deleted.', true));
        }
        
        $this->redirect('/settings/accounts/');
    }
    
    private function getIdentity($username) {
        $this->Account->Identity->contain('TwitterAccount');
        $identity = $this->Account->Identity->findByUsername($username);

        return $identity;
    }
    
    private function saveToSessionAndRedirectToPreview($data) {
    	$this->Session->write('Service.add.data', $data);
		$this->Session->write('Service.add.type', $data['service_type_id']);
		$this->redirect('/settings/accounts/add/preview/');
    }
}
