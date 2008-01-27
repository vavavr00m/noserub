<?php
/* SVN FILE: $Id:$ */
 
class AccountsController extends AppController {
    var $uses = array('Account');
    var $helpers = array('form', 'flashmessage');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Account->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        # get identity that is displayed
        $identity = $this->getIdentity($splitted['username']);
        if(!$identity) {
            # this identity is not here
            $this->redirect('/', null, true);
        }
        $this->set('about_identity', $identity['Identity']);
        
        # get all accounts
        $this->Account->recursive = 1;
        $this->Account->expects('Account.Account', 'Account.Service', 'Service.Service');
        $this->set('data', $this->Account->findAllByIdentity_id($identity['Identity']['id']));
        $this->set('session_identity', $session_identity);
        
        if($session_identity['username'] == $splitted['username']) {
            $this->set('headline', 'Your accounts');
        } else {
            $this->set('headline', $splitted['username'] . '\'s accounts');
        }
        
        $this->set('contact_accounts', $this->Account->Service->getContactAccounts());
    }
    
    function add_step_1_service_detection() {
    	$username = isset($this->params['username']) ? $this->params['username'] : '';
    	$session_identity = $this->Session->read('Identity');
    	$splitted = $this->Account->Identity->splitUsername($username);
    	
    	$this->Session->delete('Service.add.account.to.identity_id');
        $this->Session->delete('Service.add.id');
    	
    	# only logged in users can add accounts
        if(!$session_identity) {
            # this user is not logged in
            $this->flashMessage('error', 'You need to be logged in to add an account.');
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
        
    	if ($this->data) {
    		$this->ensureSecurityToken();
    		$serviceData = $this->Account->Service->detectService($this->data['Account']['url']);
    		
    		if ($serviceData) {
    			$this->Session->write('Service.add.id', $serviceData['service_id']);
	    		$data = $this->Account->Service->getInfoFromService($splitted['username'], $serviceData['service_id'], $serviceData['username']);    

	    		if (!$data) {
	                $this->Account->invalidate('url', 1);
	            } else {
	            	$this->saveToSessionAndRedirectToPreview($data, $splitted['local_username']);
	            }
    		} else {
    			$this->Session->write('Service.add.id', 8); # any rss feed
    			
    			// as we don't know the service type id yet we set the id to 3 for Text/Blog 
    			$data = $this->Account->Service->getInfoFromFeed($splitted['username'], 3, $this->data['Account']['url']);

    			if (!$data) {
    				$this->Account->invalidate('url', 1);
    			} else {
    				$this->saveToSessionAndRedirectToPreview($data, $splitted['local_username']);
    			}
    		}
    	}
    	
    	$this->set('headline', 'Specify the service url');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_step_2_preview() {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Account->Identity->splitUsername($username);
        $identity_id      = $this->Session->read('Service.add.account.to.identity_id');
        $session_identity = $this->Session->read('Identity');
        $data             = $this->Session->read('Service.add.data');
        
        # check the session vars
        if(!$identity_id || !$data) {
            # couldn't find the session vars. so either someone skipped 
            # a step, or the user was logged out during the process
            $this->redirect('/', null, true);
        }

        $data['service_id'] = $this->Session->read('Service.add.id');
        if(!empty($this->params['form'])) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
            # reset session
            $this->Session->delete('Service.add.data');

            if(isset($this->params['form']['submit'])) {
                # check if the acccount is not already there
                if($this->Account->findCount(array('identity_id' => $identity_id, 'account_url' => $data['account_url'])) == 0) {
                    # save the new account
                    $data['identity_id'] = $identity_id;
					
                    if (isset($this->data['Account']['title']) and !empty($this->data['Account']['title'])) {
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

                    if($this->Account->id && defined('NOSERUB_USE_FEED_CACHE') && NOSERUB_USE_FEED_CACHE) {
                        # save feed information to cache
                        $this->Account->Feed->store($this->Account->id, $data['items']);
                    }
                    
                    if($this->Account->id && $this->Session->read('Service.add.account.is_logged_in_user')) {
                        # test, if we can find friends from this account
                        $contacts = $this->Account->Service->getContactsFromService($this->Account->id);
                        if(!empty($contacts)) {
                            $this->Session->write('Service.add.contacts', $contacts);
                            $this->Session->write('Service.add.account_id', $this->Account->id);
                            $this->redirect('/' . $splitted['local_username'] . '/settings/accounts/add/friends/', null, true);
                        }
                    }
                    
                    $this->flashMessage('success', 'Account added.');
                }
            }
            # we're done!
            if($identity_id == $session_identity['id']) {
                # new account for the logged in user, so we redirect to his/her account settings
                $this->redirect('/' . $username . '/settings/accounts/', null, true);
            } else {
                # new account for a private contact. redirect to his/her profile
                $this->Account->Identity->recursive = 0;
                $this->Account->Identity->expects('Identity');
                $account_for_identity = $this->Account->Identity->findById($identity_id);
                $this->redirect('/' . $account_for_identity['Identity']['local_username'] . '/', null, true);
            }
        }
        // for feeds it must be possible to select the service type
        if ($data['service_id'] == 8) {
        	$this->set('service_types', $this->Account->ServiceType->find('list'));
        }
        $this->set('data', $data);
        $this->set('headline', 'Preview the data');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_step_3_friends() {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Account->Identity->splitUsername($username);
        $identity_id      = $this->Session->read('Service.add.account.to.identity_id');
        $session_identity = $this->Session->read('Identity');
        $service_id       = $this->Session->read('Service.add.id');
        $service_type_id  = $this->Session->read('Service.add.type');
        
        # check the session vars
        if(!$identity_id || !$session_identity || !$service_id || !$service_type_id) {
            # couldn't find the session vars. so either someone skipped 
            # a step, or the user was logged out during the process
            $this->redirect('/', null, true);
        }

        if(isset($this->params['form']['cancel'])) {
            # we don't neet to go further
            $this->flashMessage('success', 'Account added.');
            $this->redirect('/' . $username . '/settings/accounts/', null, true);
        }
        
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
            foreach($this->data as $item) {
                if(isset($item['action']) && $item['action'] > 0) {
                    # see, wether we should create a new contact, or add 
                    # a account to an existing one
                    if($item['action'] == 1) {
                        # first check, if the new identity is already there
                        $new_identity_username = $this->Account->Identity->sanitizeUsername($item['contactname']) . '@' . $session_identity['local_username'];
                        $new_splitted = $this->Account->Identity->splitUsername($new_identity_username);
                        
                        $identity = $this->getIdentity($new_splitted['username']);
                        if(!$identity) {
                            # create a new identity
                            $identity = array('is_local' => 1,
                                              'username' => $new_splitted['username']);
                            # saving without validation, as we have no email and no password
                            $this->Account->Identity->create();
                            if(!$this->Account->Identity->save($identity, false)) {
                                # something went wrong!
                                LogError('AccountsController::add_step_4_friends(): could not create identity "' . $new_splitted['username'] . '"');
                                continue;
                            }
                            $new_identity_id = $this->Account->Identity->id;
                        
                            # now create the contact entry
                            $contact = array('identity_id'      => $identity_id,
                                             'with_identity_id' => $new_identity_id);
                            $this->Account->Identity->Contact->create();
                            if(!$this->Account->Identity->Contact->save($contact)) {
                                # something went wrong!
                                LogError('AccountsController::add_step_4_friends(): could not create contact');
                                continue;
                            }
                        } else {
                            # the identity already exists. we assume that the
                            # contact is there, too.
                            $new_identity_id = $identity['Identity']['id'];
                        }
                        
                        # save the new identity_id to the $item, so we can
                        # go on with adding the account
                        $item['contact'] = $new_identity_id;
                    } 
                    
                    # add account to identity specified in $item['contact']
                    $account_username = $item['username'];
                    
                    $account = array('identity_id'     => $item['contact'],
                                     'service_id'      => $service_id,
                                     'service_type_id' => $service_type_id,
                                     'username'        => $account_username,
                                     'account_url'     => $this->Account->Service->getAccountUrl($service_id, $account_username),
                                     'feed_url'        => $this->Account->Service->getFeedUrl($service_id, $account_username));
                                     
                    $this->Account->create();
                    $this->Account->save($account);
                }
            }
            # we're done!
            $this->flashMessage('success', 'Account added.');
            $this->redirect('/' . $username . '/settings/accounts/', null, true);
        }
        $this->Account->recursive = 1;
        $this->Account->expects('Account', 'Service');
        $account = $this->Account->findById($this->Session->read('Service.add.account_id'));
        $this->set('headline', 'Import your social network from ' . $account['Service']['name']);

        # get data about contacts from session
        $data = $this->Session->read('Service.add.contacts');
        
        # check, if some of these contacts already are in my local
        # database. We therefore can remove them from the list
        foreach($data as $username => $item) {
            # try to find accounts with that username first
            $this->Account->recursive = 1;
            $this->Account->expects('Account', 'Identity');
            $accounts = $this->Account->findAll(array('Account.username'        => $username,
                                                      'Account.service_id'      => $service_id,
                                                      'Account.service_type_id' => $service_type_id));
            
            # we might have several accounts found, because the same account 
            # could be stored at different local identities.
            # we also don't find those, where e. a del.icio.us RSS-Feed was
            # added, instead of a del.icio.us account directly.
            foreach($accounts as $account) {
                # now see, if the identity is local to our logged
                # in identity.
                if($account['Identity']['username'] == $session_identity['local_username']) {
                    # found him/her
                    unset($data[$username]);
                    break;
                }
            }
        }
        
        # now give the data to the view
        $this->set('data', $data);
        
        $this->Account->Identity->Contact->recursive = 1;
        $this->Account->Identity->Contact->expects('Contact', 'WithIdentity');
        $data = $this->Account->Identity->Contact->findAll(array('Contact.identity_id'   => $identity_id,
                                                                 'WithIdentity.is_local' => 1,
                                                                 'WithIdentity.username LIKE "%@%"'), 
                                                           null, 'WithIdentity.username ASC');
        $contacts = array();
        foreach($data as $item) {
            $contacts[$item['WithIdentity']['id']] = $item['WithIdentity']['local_username'];
        }
        $this->set('contacts', $contacts);
    }
        
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function edit($account_id) {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $session_identity = $this->Session->read('Identity');
        $identity_id      = $session_identity['id'];

        # check the session vars
        if(!$session_identity) {
            $this->redirect('/', null, true);
        }


        # get the account
        $this->Account->recursive = 0;
        $this->Account->expects('Account');
        $data = $this->Account->find(array('id' => $account_id, 'identity_id' => $identity_id));
        if(!$data) {
            # the account for this identity could not be found
            $this->redirect('/', null, true);
        }
        
        if(!$this->data) {
            $this->set('headline', 'Edit service ' . htmlentities($data['Account']['title'], ENT_QUOTES, 'UTF-8'));
            $this->set('service_types', $this->Account->ServiceType->find('list'));
            $this->data = $data;
        } else {
            $this->Account->id = $account_id;
            if($this->Account->save($this->data, true, array('title', 'service_type_id'))) {
                $this->redirect('/' . $username . '/settings/accounts/', null, true);
            }
        }
        
        $this->render('edit');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function delete() {
        $account_id       = isset($this->params['account_id']) ? $this->params['account_id'] : '';
        $username         = isset($this->params['username'])   ? $this->params['username']   : '';
        $splitted         = $this->Account->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        # check the session vars
        if(!$username || !$session_identity) {
            # this user is not logged in
            $this->redirect('/', null, true);
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();

        if($splitted['username'] != $session_identity['username']) {
            # check, if $username belongs to the
            # logged in identities namespace
            $about_identity = $this->getIdentity($splitted['username']);
            if(!$about_identity) {
                # could not find the identity
                $this->flashMessage('alert', 'Could not find the user.');
                $this->redirect('/' . $splitted['local_username'] . '/', null, true);
            }
            if($about_identity['Identity']['namespace'] == $session_identity['local_username']) {
                $identity_id = $about_identity['Identity']['id'];
            } else {
                # this logged in user is not allowed to change something
                $this->flashMessage('alert', 'You may not delete this.');
                $this->redirect('/' . $splitted['local_username'] . '/', null, true);
            }
        }
        # check, wether the account belongs to the identity
        $this->Account->recursive = 0;
        $this->Account->expects('Account');
        if(1 == $this->Account->findCount(array('identity_id' => isset($identity_id) ? $identity_id : $session_identity['id'],
                                                'id'          => $account_id))) {
            $this->Account->id = $account_id;
            $this->Account->delete();
            $this->Account->execute('DELETE FROM ' . $this->Account->tablePrefix . 'feeds WHERE account_id=' . $account_id);
            $this->flashMessage('success', 'Account deleted.');
        }
        
        $this->redirect('/' . $splitted['local_username'] . '/settings/accounts/');
    }
    
    private function getIdentity($username) {
        $this->Account->Identity->recursive = 0;
        $this->Account->Identity->expects('Identity');
        $identity = $this->Account->Identity->findByUsername($username);

        return $identity;
    }
    
    private function saveToSessionAndRedirectToPreview($data, $username) {
    	$this->Session->write('Service.add.data', $data);
		$this->Session->write('Service.add.type', $data['service_type_id']);
		$this->redirect('/' . $username . '/settings/accounts/add/preview/');
    }
}
