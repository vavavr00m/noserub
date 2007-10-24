<?php
/* SVN FILE: $Id:$ */

// needed to unserialize the OpenID request data from the session to get the username
class Auth_OpenID_CheckIDRequest {}

class IdentitiesController extends AppController {
    var $uses = array('Identity');
    var $helpers = array('form', 'openid', 'nicetime');
    var $components = array('geocoder', 'url', 'cluster', 'openid', 'upload', 'cdn');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $filter   = isset($this->params['filter'])   ? $this->params['filter']   : '';
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $username = $splitted['username'];
        
        # sanitize filter
        switch($filter) {
            case 'photo':
            case 'video':
            case 'audio':
            case 'link':
            case 'text':
            case 'event':
            case 'micropublish':
            case 'document':
            case 'location':
                $filter = $filter; 
                break;
            
            default: 
                $filter = false;
        }
        
        $session_identity = $this->Session->read('Identity');
        
        if($splitted['namespace'] !== '' && $splitted['namespace'] != $session_identity['local_username']) {
            # don't display local contacts to anyone else, but the owner
            $data = null;
        } else {
            $this->Identity->recursive = 2;
            $this->Identity->expects('Identity.Identity', 'Identity.Account', 'Identity.Contact',
                                     'Account.Account', 'Account.Service', 'Account.ServiceType',
                                     'Service.Service',
                                     'ServiceType.ServiceType',
                                     'Contact.Contact', 'Contact.WithIdentity',
                                     'WithIdentity.WithIdentity');
            $data = $this->Identity->find(array('username'  => $username,
                                                'is_local'  => 1,
                                                'hash'      => ''));
            if($data) {
                # get the status of relationship between the logged in
                # user and the profile we're watching
                $relationship_status = '';
                if($data['Identity']['id'] == $session_identity['id']) {
                    $relationship_status = 'self';
                } else {
                    $this->Identity->Contact->recursive = 0;
                    $this->Identity->Contact->expects('Contact');
                    $is_contact = 1 == $this->Identity->Contact->findCount(array('identity_id'      => $session_identity['id'],
                                                                                 'with_identity_id' => $data['Identity']['id']));
                    $relationship_status = $is_contact ? 'contact' : 'none';
                }
                $this->set('relationship_status', $relationship_status);
                
                # get number of accounts and contacts
                # also divide between real services and contact services like AIM
                # and move 9 contacts to the view
                # @todo: sort the contacts, so that the ones with last updates are shown
                #        on top. do that with bind-/unbindModel above
                $accounts       = array();
                $communications = array();
                $contacts       = array();
                
                foreach($data['Account'] as $account) {
                    if($account['Service']['is_contact'] == 0) {
                        $accounts[] = $account;
                    } else {
                        $communications[] = $account;
                    }
                }
                $this->set('accounts', $accounts);
                $this->set('communications', $communications);
                
                $num_private_contacts = 0;
                $num_noserub_contacts = 0;
                foreach($data['Contact'] as $contact) {
                    if(strpos($contact['WithIdentity']['username'], '@') === false) {
                        $num_noserub_contacts++;
                        if(count($contacts) < 9) {
                            $contacts[] = $contact;
                        }
                    } else {
                        $num_private_contacts++;
                        if($relationship_status == 'self') {
                            if(count($contacts) < 9) {
                                $contacts[] = $contact;
                            }
                        }
                    }
                }
                $this->set('num_private_contacts', $num_private_contacts);
                $this->set('num_noserub_contacts', $num_noserub_contacts);
                $this->set('contacts', $contacts);
                
                # create $about_identity for the view
                $this->set('about_identity', $data['Identity']);
            }
        }
        
        if($data) {
            # expand all identities (domain, namespace, url)
            $data['Identity'] = array_merge($data['Identity'], $this->Identity->splitUsername($data['Identity']['username']));
            foreach($data['Contact'] as $key => $contact) {
                $data['Contact'][$key]['WithIdentity'] = array_merge($contact['WithIdentity'], $this->Identity->splitUsername($contact['WithIdentity']['username']));
            }
        
            if($splitted['username'] == $session_identity['username']) {
                $this->set('headline', 'My NoseRub Profile');
            } else {
                if($data['Identity']['latitude'] != 0 && $data['Identity']['longitude'] != 0 &&
                   $session_identity['latitude'] != 0 && $session_identity['longitude'] != 0) {
                    $this->set('distance', $this->geocoder->distance($data['Identity']['latitude'], $data['Identity']['longitude'],
                                                                     $session_identity['latitude'], $session_identity['longitude']));
                }
                $this->set('headline', $splitted['local_username'] . '\'s NoseRub page');
            }
        } else {
            $this->set('headline', 'Username could not be found!');
        }

        # get all items for those accounts
        $items = array();
        if(is_array($data['Account'])) {
            foreach($data['Account'] as $account) {
                if(!$filter || $account['ServiceType']['token'] == $filter) {
                    if(defined('NOSERUB_USE_FEED_CACHE') && NOSERUB_USE_FEED_CACHE) {
                        $new_items = $this->Identity->Account->Feed->access($account['id'], 5, false);
                    } else {
                        $new_items = $this->Identity->Account->Service->feed2array($username, $account['service_id'], $account['service_type_id'], $account['feed_url'], 5, false);
                    }
                    if($new_items) {
                        $items = array_merge($items, $new_items);
                    }
                }
            }
            usort($items, 'sort_items');
            $items = $this->cluster->create($items);
        }
    
        $this->set('data', $data);
        $this->set('items', $items);
        $this->set('session_identity', $session_identity);
        $this->set('filter', $filter);
    }
    
    function social_stream() {
        $filter = isset($this->params['filter']) ? $this->params['filter']   : '';
        
        # sanitize filter
        switch($filter) {
            case 'photo':
            case 'video':
            case 'audio':
            case 'link':
            case 'text':
            case 'event':
            case 'micropublish':
            case 'document':
            case 'location':
                $filter = $filter; 
                break;
            
            default: 
                $filter = false;
        }
        
        $this->Identity->recursive = 2;
        $this->Identity->expects('Identity.Identity', 'Identity.Account', 
                                 'Account.Account', 'Account.Service', 'Account.ServiceType',
                                 'Service.Service',
                                 'ServiceType.ServiceType');
        $data = $this->Identity->findAll(array('frontpage_updates' => 1,
                                               'is_local'  => 1,
                                               'hash'      => '',
                                               'NOT username LIKE "%@%"'),
                                         null, null, 10);

        # extract the identities
        
        $items      = array();
        $identities = array();
        foreach($data as $identity) {
            
            # extract the identities
            if(count($identities) < 9) {
                $identities[] = $identity['Identity'];
            }
            
            # get all items for those accounts
            if(is_array($identity['Account'])) {
                foreach($identity['Account'] as $account) {
                    if(!$filter || $account['ServiceType']['token'] == $filter) {
                        if(defined('NOSERUB_USE_FEED_CACHE') && NOSERUB_USE_FEED_CACHE) {
                            $new_items = $this->Identity->Account->Feed->access($account['id'], 5, false);
                        } else {
                            $new_items = $this->Identity->Account->Service->feed2array($identity['Identity']['username'], $account['service_id'], $account['service_type_id'], $account['feed_url'], 5, false);
                        }
                        if($new_items) {
                            $items = array_merge($items, $new_items);
                        }
                    }
                }
            }
        }
        usort($items, 'sort_items');
        $items = $this->cluster->create($items);

        $this->set('data', $data);
        $this->set('identities', $identities);
        $this->set('items', $items);
        $this->set('filter', $filter);
        
        $this->set('headline', 'All public social activities');
        $this->render('social_stream');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function profile_settings() {
        $this->checkSecure();
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        if($this->data) {
            # get identity again to check, where we have changes
            $this->Identity->recursive = 0;
            $this->Identity->expects('Identity');
            $identity = $this->Identity->findById($session_identity['id']);

            # geocode the address, if neccessary
            if($identity['Identity']['address'] != $this->data['Identity']['address'] || 
               ($identity['Identity']['longitude'] == 0 &&
                $identity['Identity']['latitude'] == 0)) {
                $geolocation = $this->geocoder->get($this->data['Identity']['address']);
                if($geolocation !== false) {
                    $this->data['Identity']['latitude']  = $geolocation['latitude'];
                    $this->data['Identity']['longitude'] = $geolocation['longitude'];
                } else {
                    $this->data['Identity']['latitude']  = 0;
                    $this->data['Identity']['longitude'] = 0;
                }
            } else {
                $this->data['Identity']['latitude']  = $identity['Identity']['latitude'];
                $this->data['Identity']['longitude'] = $identity['Identity']['longitude'];
            }
            
            $path = STATIC_DIR . 'avatars' . DS;
            
            # check, if photo should be removed
            if(isset($this->data['Identity']['remove_photo']) && $this->data['Identity']['remove_photo'] == 1) {
                @unlink($path . $identity['Identity']['photo'] . '.jpg');
                @unlink($path . $identity['Identity']['photo'] . '-small.jpg');
                $identity['Identity']['photo'] = '';
            }
            
            # save the photo, if neccessary
            if($this->data['Identity']['photo']['error'] != 0) {
                $this->data['Identity']['photo'] = $identity['Identity']['photo'];
            } else {                
                $filename = $this->upload->add($this->data['Identity']['photo'], $identity, $path);
                if($filename) {
                    $this->data['Identity']['photo'] = $filename;
                    
                    if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
                        # store to CDN
                        $this->cdn->copyTo($path . $filename . '.jpg',       'avatars/'.$filename.'.jpg');
                        $this->cdn->copyTo($path . $filename . '-small.jpg', 'avatars/'.$filename.'-small.jpg');
                    }
                }
            }   
             
            $saveable = array('firstname', 'lastname', 'about', 'photo', 'sex', 'address', 'address_shown', 'latitude', 'longitude', 'modified');
            
            $this->Identity->id = $session_identity['id'];
            $this->Identity->save($this->data, false, $saveable);
        } else {
            $this->Identity->recursive = 0;
            $this->Identity->expects('Identity');
            $this->data = $this->Identity->findById($session_identity['id']);
        }
        
        $this->set('headline', 'Settings for my NoseRub Account');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function privacy_settings() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        if($this->data) {
            $saveable = array('frontpage_updates');
            $this->Identity->id = $session_identity['id'];
            $this->Identity->save($this->data, true, $saveable);
        } else {
            $this->Identity->id = $session_identity['id'];
            $this->data['Identity']['frontpage_updates'] = $this->Identity->field('frontpage_updates');
        }
        
        $this->set('headline', 'Your privacy settings');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function password_settings() {
        $this->checkSecure();
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] || isset($session_identity['openid'])) {
            # this is not the logged in user or the user used an OpenID to register
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        if($this->data) {
            $data = $this->data;
            $data['Identity']['username'] = $splitted['username'];
            $data['Identity']['password'] = $data['Identity']['old_passwd'];
            if($this->Identity->check($data)) {
                # old password is ok
                $this->data['Identity']['password'] = md5($this->data['Identity']['passwd']);
                $this->Identity->id = $session_identity['id'];
                $this->Identity->save($this->data, true, array('password'));
            } else {
                $this->Identity->invalidate('old_passwd');
            }
        }
        
        $this->set('headline', 'Change your password');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function account_settings() {
        $this->checkSecure();
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        if($this->data) {
            $data = $this->data;
            
            if (!isset($session_identity['openid'])) {
	            $data['Identity']['username'] = $splitted['username'];
	            $data['Identity']['password'] = $data['Identity']['passwd'];
	            if($this->Identity->check($data)) {
	            	$this->deleteAccount($session_identity, $this->data['Identity']['confirm']);
	            } else {
	                $this->Identity->invalidate('passwd');
	            }
            } else {
				$this->deleteAccount($session_identity, $this->data['Identity']['confirm']);
            }
        }
        
        $this->set('headline', 'Delete your account');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function login() {
        $this->checkSecure();
        $sessionKeyForOpenIDRequest = 'Noserub.lastOpenIDRequest';
        
        if(!empty($this->data)) {
            $identity = $this->Identity->check($this->data);
            if($identity) {
                $this->Session->write('Identity', $identity['Identity']);
                if ($this->Session->check($sessionKeyForOpenIDRequest)) {
                	$this->redirect('/auth', null, true);
                } else {
                	$url = $this->url->http('/' . urlencode(strtolower($identity['Identity']['local_username'])) . '/');
                	$this->redirect($url, null, true);
                }
            } else {
                $this->set('form_error', 'Login not possible');
            }
        } else {
        	if ($this->Session->check($sessionKeyForOpenIDRequest)) {
        		$request = $this->Session->read($sessionKeyForOpenIDRequest);
        		$this->data['Identity']['username'] = $request->identity;
        	}
        }
        
        $this->set('headline', 'Login with existing NoseRub account');
    }
    
    function login_with_openid() {
    	$this->set('headline', 'Login with OpenID');
    	
    	if (!empty($this->data)) {
    		$this->authenticateOpenID($this->data['Identity']['openid'], '/pages/login/withopenid');
    	} else {
    		if (count($this->params['url']) > 1) {
    			$response = $this->getOpenIDResponseIfSuccess();
    			$identity = $this->Identity->checkOpenID($response->identity_url);
    			
    			if ($identity) {
    				$this->Session->write('Identity', $identity['Identity']);
    				$url = $this->url->http('/' . urlencode(strtolower($identity['Identity']['local_username'])) . '/');
                	$this->redirect($url, null, true);
    			} else {
    				$this->set('form_error', 'Login not possible');
    			}
    		}
    	}
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function logout() {
        $this->Session->delete('Identity');
        $this->redirect($this->url->http('/'), null, true);
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function register() {
        $this->checkSecure();
        
        if(NOSERUB_REGISTRATION_TYPE != 'all') {
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }

        if(!empty($this->data)) {
            if($this->Identity->register($this->data)) {
                $url = $this->url->http('/pages/register/thanks/');
                $this->redirect($url, null, true);
            }
        } else {
            # set default value for this privacy setting
            $this->data = array('Identity' => array('frontpage_updates' => 1));
        }

        $this->set('headline', 'Register a new NoseRub account');
    }
    
    function register_with_openid_step_1() {
    	$this->set('headline', 'Register a new NoseRub account - Step 1/2');

    	if (!empty($this->data)) {
    		$this->authenticateOpenID($this->data['Identity']['openid'], '/pages/register/withopenid', array('email'));
    	} else {
    		if (count($this->params['url']) > 1) {
    			$response = $this->getOpenIDResponseIfSuccess();

    			$identity = $this->Identity->checkOpenID($response->identity_url);
    			
    			if ($identity) {
    				# already registered, so we perform a login
    				$this->Session->write('Identity', $identity['Identity']);
    				$url = $this->url->http('/' . urlencode(strtolower($identity['Identity']['local_username'])) . '/');
                	$this->redirect($url, null, true);
    			} else {
	    			$sregResponse = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
    				$sreg = $sregResponse->contents();
    				
    				$this->Session->write('Registration.openid', $response->identity_url);
					$this->Session->write('Registration.openid_server_url', $response->endpoint->server_url);
    				
	    			if (@$sreg['email']) {
	    				$this->Session->write('Registration.email', $sreg['email']);
	    			}
	
	    			$this->redirect('/pages/register/withopenid/step2', null, true);
    			}
    		}
    	}
    }
    
    function register_with_openid_step_2() {
    	if (!$this->Session->check('Registration.openid')) {
    		$this->redirect('/pages/register/withopenid', null, true);
    	}
    	
    	$this->set('headline', 'Register a new NoseRub account - Step 2/2');

    	if (!empty($this->data)) {
    		$this->data['Identity']['openid'] = $this->Session->read('Registration.openid');
    		$this->data['Identity']['openid_server_url'] = $this->Session->read('Registration.openid_server_url');
    		
    		if($this->Identity->register($this->data)) {
	    		$this->Session->delete('Registration.openid');
	    		$this->Session->delete('Registration.openid_server_url');
	    		$this->Session->delete('Registration.email');
    			$this->redirect('/pages/register/thanks/', null, true);
            }
    	} else {
    		if ($this->Session->check('Registration.email')) {
    			$this->data['Identity']['email'] = $this->Session->read('Registration.email');
    		}
    		
    		$this->data['Identity']['frontpage_updates'] = 1;
    	}
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function verify() {
        $hash = isset($this->params['hash']) ? $this->params['hash'] : '';
        
        $this->set('verify_ok', $this->Identity->verify($hash));
        
        $this->set('headline', 'Verify your e-mail address');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function register_thanks() {
        $this->set('headline', 'Thanks for your registration!');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function account_deleted() {
        $this->set('headline', 'Account deleted');
    }
    
    /**
     * Synchronizes the given identity from another server
     * to this local NoseRub instance
     *
     * @param  string admin_hash (through $this->params)
     * @param  string username (through $this->params)
     * @return 
     * @access 
     */
    function jobs_sync() {
        $admin_hash  = isset($this->params['admin_hash'])  ? $this->params['admin_hash'] : '';
        $identity_id = isset($this->params['identity_id']) ? $this->params['identity_id'] : 0;
        
        if($admin_hash != NOSERUB_ADMIN_HASH ||
           $admin_hash == '' ||
           !$identity_id) {
            # there is nothing to do for us here
            return false;
        }
        
        # see, if we can find the identity.
        # it should be in our database already.
        $this->Identity->recursive = 0;
        $this->Identity->expects('Identity');
        $identity = $this->Identity->findById($identity_id);

        if(!$identity || $identity['Identity']['is_local'] == 1) {
            # we could not find it, or this is a local identity
            return false;
        }
        
        return $this->Identity->sync($identity_id, $identity['Identity']['username']);
    }
    
    /**
     * sync all identities with their remote server
     *
     * @param  
     * @return 
     * @access 
     */
    function jobs_sync_all() {
        $admin_hash = isset($this->params['admin_hash']) ? $this->params['admin_hash'] : '';
        
        if($admin_hash != NOSERUB_ADMIN_HASH ||
           $admin_hash == '') {
            # there is nothing to do for us here
            return false;
        }
        
        # get all not local identities
        $this->Identity->recursive = 0;
        $this->Identity->expects('Identity');
        $identities = $this->Identity->findAll(array('is_local' => 0), null, 'last_sync ASC');
        $synced = array();
        foreach($identities as $identity) {
            $this->Identity->sync($identity['Identity']['id'], $identity['Identity']['username']);       
            $synced[] = $identity['Identity']['username'];
        }

        $this->set('data', $synced);
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function shell_sync_all() {
        $this->params['admin_hash'] = NOSERUB_ADMIN_HASH;
        $this->jobs_sync_all();
        $this->render('jobs_sync_all');
    }
    
    private function authenticateOpenID($openid, $returnTo, $required = array(), $optional = array()) {
    	try {
    		$this->openid->authenticate($openid, 
    									'http://'.$_SERVER['SERVER_NAME'].$returnTo, 
    									'http://'.$_SERVER['SERVER_NAME'], 
    									$required,
    									$optional);
    	} catch (InvalidArgumentException $e) {
    		$this->Identity->invalidate('openid', 'invalid_openid');
			$this->render();
			exit;
    	} catch (Exception $e) {
    		echo $e->getMessage();
    		exit();
    	}
    }
    
    private function deleteAccount($identity, $confirm) {
    	if($confirm == 0) {
			$this->set('confirm_error', 'In order to delete your account, please check the check box.');
		} else if($confirm == 1) {
			$identityId = $identity['id'];
			$this->Identity->Account->deleteByIdentityId($identityId);
			$this->Identity->Contact->deleteByIdentityId($identityId, $identity['local_username']);
			$this->Identity->block($identityId);
			$this->Session->delete('Identity');
			$this->redirect('/pages/account/deleted/', null, true);
		}
    }
    
    private function getOpenIDResponseIfSuccess() {
    	$response = $this->openid->getResponse();
    			
    	if ($response->status == Auth_OpenID_CANCEL) {
    		$this->Identity->invalidate('openid', 'verification_cancelled');
    		$this->render();
    		exit;
    	} elseif ($response->status == Auth_OpenID_FAILURE) {
    		$this->Identity->invalidate('openid', 'openid_failure');
    		$this->set('errorMessage', $response->message);
    		$this->render();
    		exit;
    	} elseif ($response->status == Auth_OpenID_SUCCESS) {
    		return $response;
    	}
    }
}