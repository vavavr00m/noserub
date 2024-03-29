<?php
/* SVN FILE: $Id:$ */

// needed to unserialize the OpenID request data from the session to get the username
class Auth_OpenID_CheckIDRequest {}

class IdentitiesController extends AppController {
    public $uses = array('Identity');
    public $helpers = array('openid', 'nicetime');
    public $components = array(
        'geocoder', 'url', 'cluster', 'openid', 'cdn', 'Cookie'
    );
    
    /**
     * Displays profile page of an identity 
     */
    public function profile() {
        $this->checkUnsecure();
        $this->grantAccess('all');
        
        Context::setPage('profile.home');
        
        $this->render('profile');
    }
    
    /**
     * Displays the social stream of the
     * logged_in_identitiy's contacts
     */
    public function social_stream() {
        $this->checkUnsecure();
        $this->grantAccess('self');        
        
        Context::setPage('activities');
    }
    
	/**
     * Displays favorite items of a user 
 	 */
    public function favorites() {
    }
    
	/**
	 * Displays comments of a user
	 */
	public function comments() {
    }

    public function vcard() {
        $this->RequestHandler->setContent('vcf', 'text/x-vcard');
        $this->RequestHandler->respondAs('vcf');
        $this->layout = 'empty';
    }
    
    public function feed() {
        $identity_id = Context::read('identity.id');
        $items = array();
        if($identity_id) {
            $items = $this->Identity->Entry->getForDisplay(
                array(
                    'identity_id' => $identity_id
                    ),
                20, 
                false
            );
            if($items) {
                usort($items, 'sort_items');
                $items = $this->cluster->removeDuplicates($items);
            }
        }
        $this->set('items', $items);
        $this->layout = 'feed_rss';
    }
    
    public function send_message() {
        $this->grantAccess('user');
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');

        if(!$session_identity || $splitted['namespace'] != '' || $splitted['local'] == 0) {
            # this user is not the logged in, or this is a private
            # contact, or not local
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        # get Identity
        $this->Identity->contain();
        $about_identity = $this->Identity->findByUsername($splitted['username']);
        $name = empty($about_identity['Identity']['name']) ? $about_identity['Identity']['single_username'] : $about_identity['Identity']['name'];
        $this->set('headline', __('Send a message to ', true) . $name);
        $this->set('data', $about_identity);
        $this->set('base_url_for_avatars', $this->Identity->getBaseUrlForAvatars());
        
        $send_allowed = true;
        # check the users privacy setting
        if($about_identity['Identity']['allow_emails'] == 0) {
            $this->flashMessage('alert', __('You may not send a message to ', true) . $name);
            $send_allowed = false;
        } else if($about_identity['Identity']['allow_emails'] == 1) {
            $has_contact = $this->Identity->Contact->hasAny(array('identity_id'      => $about_identity['Identity']['id'],
                                                                  'with_identity_id' => $session_identity['id']));
        
            if(!$has_contact) {
                $this->flashMessage('alert', __('You may not send a message to ', true) . $name);
                $send_allowed = false;
            }
        }
        
        if($this->data && $send_allowed) {
            if(empty($this->data['Message']['subject'])) {
                $this->flashMessage('alert', __('You need to specify a subject.', true));
            } 
            
            if(empty($this->data['Message']['text'])) {
                $this->flashMessage('alert', __('You need to specify a text.', true));
            }

            if(!empty($this->data['Message']['subject']) && !empty($this->data['Message']['text'])) {
                # send the mail now
                $subject = $this->data['Message']['subject'];
                # sanitize some characters, so no header escaping can happen
                $clean_subject = str_replace(':', '', $subject);
                $clean_subject = str_replace("\n", '', $clean_subject);
                $clean_subject = str_replace("\r", '', $clean_subject);
                $clean_subject = strip_tags($clean_subject);
                $text    = strip_tags($this->data['Message']['text']);
            
                $msg  = __('Hi ', true) . $name . "\n\n";
                $msg .= __('You got a message from ', true) . 'http://' . $session_identity['username'] . '/' . "\n\n";
                $msg .= '----------------------------------------------------------' . "\n";
                $msg .= __('Subject: ', true) . $subject . "\n";
                $msg .= __('Text:', true) . "\n" . $text . "\n\n";
                $msg .= '----------------------------------------------------------' . "\n\n";
                $msg .= __('If you want to reply to this message, go to ', true) . 'http://' . $session_identity['username'] . '/' . "\n";
            
                $email = $about_identity['Identity']['email'];
                if(!mail($email, '['. Context::read('network.name') . '] ' . $clean_subject, $msg, 'From: ' . Configure::read('NoseRub.email_from'))) {
                    $this->log('mail could not be sent: '.$email . ' / ' . $clean_subject);
                    $this->flashMessage('alert', __('Your Message could not be delivered to ', true) . $name);
                } else {
                    $this->log('mail sent: ' . $email . ' / ' . $clean_subject, LOG_DEBUG);
                    $this->flashMessage('success', __('Your Message was sent to ', true) . $name);
                    $this->redirect('/' . $splitted['local_username'] . '/');
                }
            }
        }
    }
    
    public function profile_settings() {
        $this->checkSecure();
        $this->grantAccess('self');
        
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
               
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
            # get identity again to check, where we have changes
            $this->Identity->contain();
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
            
            if(isset($this->data['Identity']['remove_photo']) && $this->data['Identity']['remove_photo'] == 1) {
            	$this->deleteAvatars($identity['Identity']['photo']);
                $identity['Identity']['photo'] = '';
                $this->data['Identity']['photo'] = $identity['Identity']['photo'];
            } else if(isset($this->data['Identity']['use_gravatar']) && 
                      $this->data['Identity']['use_gravatar'] == 1 && 
                      $this->data['Identity']['photo']['error'] == 4) {
            	# use gravatar image
            	$md5 = md5($identity['Identity']['email']);
            	$this->data['Identity']['photo'] = 'http://gravatar.com/avatar/' . $md5;
            	$this->Identity->Entry->addPhotoChanged($identity['Identity']['id']);
            } else if($this->data['Identity']['photo']['error'] != 0) {
            	# save the photo, if neccessary
                $this->data['Identity']['photo'] = $identity['Identity']['photo'];
            } else {                
                $this->Identity->id = $session_identity['id'];
                $filename = $this->Identity->uploadPhotoByForm($this->data['Identity']['photo']);
                if($filename) {
                    $this->data['Identity']['photo'] = $filename;
                    if(Configure::read('NoseRub.use_cdn')) {
                        $this->copyAvatarsToCdn($filename);
                    }
                    $this->Identity->Entry->addPhotoChanged($identity['Identity']['id']);
                }
            }   
             
            $saveable = array(
                'firstname', 'lastname', 'about', 'sex',
                'title', 'keywords', 'photo', 'address', 
                'address_shown', 'latitude', 'longitude', 'modified'
            );
            
            $this->Identity->id = $session_identity['id'];
            $this->Identity->save($this->data, false, $saveable);
            $this->sendUpdateToOmbSubscribers($this->data);
            
            $this->flashMessage('success', __('Changes have been saved.', true));
            
        } else {
            $this->Identity->contain();
            $this->data = $this->Identity->findById($session_identity['id']);
        }
        
        $this->set('headline', __('My profile settings', true));
    }
    
    public function display_settings() {
        $this->grantAccess('self');
        
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
         if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();

            # sanitize all the filters
            $sanitized_filters = array();
            foreach($this->data['Identity']['overview_filters'] as $filter) {
                if($this->ServiceType->sanitizeFilter($filter)) {
                    $sanitized_filters[] = $filter;
                }
            }
            
            $this->Identity->id = $session_identity['id'];
            $new_value = join(',', $sanitized_filters);
            $this->Identity->saveField('overview_filters', $new_value);
            
            $this->Session->write('Identity.overview_filters', $new_value);
            
            $this->flashMessage('success', __('Display options have been saved.', true));
        } else {
            $this->Identity->id = $session_identity['id'];
            $this->data['Identity']['overview_filters'] = explode(',', $this->Identity->field('overview_filters'));
        }
        
        $this->set('filters', $this->ServiceType->getFilters());
        $this->set('headline', __('Configure your display options', true));
    }
    
    public function privacy_settings() {
        $this->grantAccess('self');
        
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
                
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
            # get the frontpage update, so we know if anything changed
            $this->Identity->id = $session_identity['id'];
            $frontpage_updates = $this->Identity->field('frontpage_updates');
            
            if($frontpage_updates != $this->data['Identity']['frontpage_updates']) {
                $this->Identity->Entry->updateRestriction($session_identity['id'], $frontpage_updates == 1 ? 1 : 0);
            }
            # save all settings
            $saveable = array(
                'frontpage_updates', 
                'allow_emails',
                'notify_contact',
                'notify_comment',
                'notify_favorite'
            );
            $this->Identity->save($this->data, true, $saveable);
            
            $this->flashMessage('success', __('Privacy settings have been saved.', true));
        } else {
            $this->Identity->contain();
            $this->data = $this->Identity->find(
                'first',
                array(
                    'conditions' => array(
                        'id' => $session_identity['id']
                    ),
                    'fields' => array(
                        'frontpage_updates',
                        'allow_emails',
                        'notify_contact',
                        'notify_comment',
                        'notify_favorite'
                    )
                )
            );
        }
        
        $this->set('headline', __('Your privacy settings', true));
    }
    
    public function password_settings() {
        $this->checkSecure();
        $this->grantAccess('self');
        
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
                
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();

            $data = $this->data;
            $data['Identity']['username'] = $splitted['username'];
            $data['Identity']['password'] = $data['Identity']['old_passwd'];
            if($this->Identity->check($data)) {
                # old password is ok, now check the new
                if(!$data['Identity']['passwd']) {
                    $this->flashMessage('alert', __('You need to specify a new password.', true));
                } else if($data['Identity']['passwd'] != $data['Identity']['passwd2']) {
                    $this->flashMessage('alert', __('The new password was not entered twice the same.', true));
                } else {
                    $this->data['Identity']['password'] = md5($this->data['Identity']['passwd']);
                    $this->Identity->id = $session_identity['id'];
                    $this->Identity->save($this->data, true, array('password'));
                
                    $this->flashMessage('success', __('The new password has been saved.', true));
                }
            } else {
                $this->flashMessage('alert', __('The old password was not correct.', true));
            }
        }
        
        $this->set('session_identity', $session_identity);
        $this->set('headline', __('Password settings', true));
    }

    public function login() {
    	$this->checkSecure();
    	$sessionKeyForOpenIDRequest = 'Noserub.lastOpenIDRequest';
    	
    	if(!empty($this->data) || $this->openid->isOpenIDResponse()) {
    		if(isset($this->data['Identity']['username'])) {
    			$identity = $this->Identity->check($this->data);
    		} else {
    			$identity = $this->loginWithOpenID();
    		}
    		
    		if($identity) {
    		    $this->Session->write('Config.language', $identity['Identity']['language']);
    		    
                $this->Session->write('Identity', $identity['Identity']);
                if($this->Session->check($sessionKeyForOpenIDRequest)) {
                	$this->redirect('/auth');
                } else {
                    # check, if we should remember this user
                    if($this->data['Identity']['remember'] == 1) {
                        $this->Cookie->write('li', $identity['Identity']['id'], true, '4 weeks');
                    } 
                    
                    if(!$this->Session->check('Login.success_url')) {
                        if($this->Session->read('Login.is_guest')) {
	                        $this->flashMessage('success', __('Welcome! It\'s nice to have you here.', true));
	                        $url = $this->url->http('/');
                        } else {
                            $this->flashMessage('success', __('Welcome! It\'s nice to have you back.', true));
                            $url = $this->url->http('/activities/');
                        }	                    
                    } else {
                        $this->flashMessage('success', __('Welcome! It\'s nice to have you back.', true));
                    	$url = $this->url->http($this->Session->read('Login.success_url'));
                    	$this->Session->delete('Login.success_url');
                    }
                    
                    $this->Identity->id = $identity['Identity']['id'];
                    $this->Identity->saveField('last_login', date('Y-m-d H:i:s'));
                    
                    $this->redirect($url);
                }
            } else {
                $this->set('form_error', __('Login not possible', true));
            }
    	} else {
    		if ($this->Session->check($sessionKeyForOpenIDRequest)) {
        		$request = $this->Session->read($sessionKeyForOpenIDRequest);
        		$this->data['Identity']['username'] = $request->identity;
        	}
    	}
    	
    	$this->set('headline', __('Login with existing NoseRub account', true));
    }
    
    // XXX hack for http://code.google.com/p/noserub/issues/detail?id=240 
    public function login_with_openid() {
    	$this->checkUnsecure();
    	$openid = $this->Session->read('OpenidLogin.openid');
    	
    	if($openid) {
    		$returnTo = 'https://'.$_SERVER['SERVER_NAME'].$this->webroot.'pages/login';
    		$realm = str_replace('http://', 'https://', FULL_BASE_URL);
		    $this->authenticateOpenID($openid, $returnTo, $realm);
    		exit;
    	}
    	
    	$this->redirect(Router::url('/pages/login'));
    }
    
    // this method hides the fact that two requests are necessary when login 
    // with an OpenID 
    private function loginWithOpenID() {
    	$protocol = 'http://';
    	
    	if(Context::read('network.use_ssl')) {
    		$protocol = 'https://';
    	}
    	
    	$returnTo = $protocol.$_SERVER['SERVER_NAME'].$this->webroot.'pages/login';
    	
    	if(!empty($this->data)) {
    		$this->Session->write('OpenidLogin.remember', $this->data['Identity']['remember']);
    		
    		if(Context::read('network.use_ssl')) {
    			$this->Session->write('OpenidLogin.openid', $this->data['Identity']['openid']);
    			// we switch to http for submitting the OpenID to the OpenID provider to avoid browser warning 
    			$this->redirect(str_replace('https', 'http', Router::url('/pages/login/openid', true)));
    		}
    		
    		$this->authenticateOpenID($this->data['Identity']['openid'], $returnTo, FULL_BASE_URL);
    		return;
    	} else {
    		$this->data['Identity']['remember'] = $this->Session->read('OpenidLogin.remember');
    		$this->Session->delete('OpenidLogin.remember');
    		$response = $this->getOpenIDResponseIfSuccess($returnTo);
    		$identity = $this->Identity->getIdentityByOpenIDResponse($response);
    		
    		if($identity) {
    			if ($identity['Identity']['network_id'] == 0) {
    				$this->Session->write('Login.is_guest', true);
    			}
    			return $identity;
    		}
    		
    		$this->Session->write('Login.is_guest', true);
    		return $this->Identity->createGuestIdentity($response->identity_url);
    	}
    }
        
    public function logout() {
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # delete login cookie
        $this->Cookie->delete('li');
        
        $this->Session->delete('Login');
        $this->Session->delete('Identity');
        $this->Session->delete('Admin');
        $this->redirect($this->url->http('/'));
    }
    
    public function account_deleted() {
        $this->set('headline', __('Account deleted', true));
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
    public function jobs_sync() {
        $admin_hash  = isset($this->params['admin_hash'])  ? $this->params['admin_hash'] : '';
        $identity_id = isset($this->params['identity_id']) ? $this->params['identity_id'] : 0;
        
        if($admin_hash != Configure::read('NoseRub.admin_hash') ||
           $admin_hash == '' ||
           !$identity_id) {
            # there is nothing to do for us here
            return false;
        }
        
        # see, if we can find the identity.
        # it should be in our database already.
        $this->Identity->contain();
        $identity = $this->Identity->findById($identity_id);

        if(!$identity || $identity['Identity']['network_id'] == Context::NetworkId()) {
            # we could not find it, or this is a local identity
            return false;
        }
        
        $result = $this->Identity->sync($identity_id, $identity['Identity']['username']);
        if($result) {
            # check, if there is a new photo
            $this->Identity->id = $identity_id;
            $this->recursive = 0;
            $this->Identity->contain();
            $data = $this->Identity->read();
            if($data['Identity']['photo'] && strpos($data['Identity']['photo'], 'ttp://') > 0) {
                $filename = $this->Identity->uploadPhotoByUrl($data['Identity']['photo']);
                if(Configure::read('NoseRub.use_cdn')) {
                    $this->copyAvatarsToCdn($filename);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * sync all identities with their remote server 
     */
    public function jobs_sync_all() {
        $admin_hash = isset($this->params['admin_hash']) ? $this->params['admin_hash'] : '';
        
        if($admin_hash != Configure::read('NoseRub.admin_hash') ||
           $admin_hash == '') {
            # there is nothing to do for us here
            return false;
        }
        
        # get all not local identities
        $this->Identity->contain();
        $identities = $this->Identity->find(
            'all', 
            array(
                'conditions' => array(
                    'network_id' => 0
                ), 
                'order' => array(
                    'last_sync ASC'
                )
            )
        );
        $synced = array();
        foreach($identities as $identity) {
            $this->Identity->sync($identity['Identity']['id'], $identity['Identity']['username']);       
            $synced[] = $identity['Identity']['username'];
        }

        $this->set('data', $synced);
    }
    
    public function shell_sync_all() {
        $this->params['admin_hash'] = Configure::read('NoseRub.admin_hash');
        $this->jobs_sync_all();
        $this->render('jobs_sync_all');
    }
    
    public function cron_sync_all() {
        $cron_hash= isset($this->params['cron_hash'])  ? $this->params['cron_hash'] : '';
        
        if($cron_hash != Configure::read('NoseRub.cron_hash') ||
           $cron_hash == '') {
            # there is nothing to do for us here
            $this->set('data', __('Value for NoseRub.cron_hash from noserub.php does not match or is empty!', true));
            $this->render('jobs_sync_all');
            return;
        }
        
        $this->shell_sync_all();
    }
    
    public function yadis() {
    	Configure::write('debug', 0);
    	$this->layout = 'xml';
    	header('Content-type: application/xrds+xml');
		$this->set('server', Router::url('/', true));
    }
    
	public function xrds() {
		$username = isset($this->params['username']) ? $this->params['username'] : '';
		Configure::write('debug', 0);
		$this->layout = 'xml';
		header('Content-type: application/xrds+xml');
		$this->set('server', Router::url('/', true));
		$this->set('username', $username);
	}
    
    private function authenticateOpenID($openid, $returnTo, $realm, $required = array(), $optional = array()) {
    	try {
    		$this->openid->authenticate($openid, 
    									$returnTo, 
    									$realm,
    									$required,
    									$optional);
    	} catch (InvalidArgumentException $e) {
    		$this->Identity->invalidate('openid', 'invalid_openid');
			$this->render();
			return;
    	} catch (Exception $e) {
    		echo $e->getMessage();
    		exit;
    	}
    }
    
    private function getFilter($session_identity) {
    	$filter = isset($this->params['filter']) ? $this->params['filter'] : '';
    	$filter = $this->ServiceType->sanitizeFilter($filter);
    	
    	if($filter == '') {
        	$filter = isset($session_identity['overview_filters']) ? explode(',', $session_identity['overview_filters']) : $this->ServiceType->getDefaultFilters();
        } else {
            $filter = array($filter);
        }
        
        return $filter;
    }
    
    private function getOpenIDResponseIfSuccess($returnTo) {
    	$response = $this->openid->getResponse($returnTo);
    			
    	if ($response->status == Auth_OpenID_CANCEL) {
    		$this->Identity->invalidate('openid', 'verification_cancelled');
    		$this->render();
    		return;
    	} elseif ($response->status == Auth_OpenID_FAILURE) {
    		$this->Identity->invalidate('openid', 'openid_failure');
    		$this->set('errorMessage', $response->message);
    		$this->render();
    		return;
    	} elseif ($response->status == Auth_OpenID_SUCCESS) {
    		return $response;
    	}
    }
    
    private function copyAvatarsToCdn($avatarName) {
    	$fileNames = $this->getAvatarFileNames($avatarName);
    	
    	foreach ($fileNames as $fileName) {
    		$this->cdn->copyTo(AVATAR_DIR . $fileName, 'avatars/' . $fileName);
    	}
    }
    
	private function deleteAvatars($avatarName) {
		$fileNames = $this->getAvatarFileNames($avatarName);
		
		foreach ($fileNames as $fileName) {
			@unlink(AVATAR_DIR . $fileName);
		}
    }
    
    private function getAvatarFileNames($avatarName) {
    	return array($avatarName . '.jpg', 
    				 $avatarName . '-medium.jpg', 
    				 $avatarName . '-small.jpg');
    }
    
    private function sendUpdateToOmbSubscribers($data) {
    	App::import('Component', 'OmbRemoteService');
    	OmbRemoteServiceComponent::createRemoteService()->updateProfile($this->Identity->id, $data);
    }
    
    /**
     * sets the language
     *
     * todo: when the user is logged in, selected language should be saved
     *       to the database
     */
    public function switch_language() {
        $this->grantAccess('all');
        
        $language = $this->data['Config']['language'];
        $languages = Configure::read('Languages');
        if(!isset($languages[$language])) {
            $language = 'en-en';
        } 
        
        $this->Session->write('Config.language', $language);
        # now set the language
        $this->L10n->get($language);

        setlocale(LC_ALL, 
            substr($this->L10n->locale, 0, 3) .
            strtoupper(substr($this->L10n->locale, 3, 2)) . 
            '.' . $this->L10n->charset
        );
        
        $session_identity = $this->Session->read('Identity');
        if($session_identity) {
            # user is logged in, so save it to the db
            $this->Identity->id = $session_identity['id'];
            $this->Identity->saveField('language', $language);
            $this->Session->write('Identity.language', $language);
        }
        
        $this->redirect($this->referer());
    }
    
    /**
     * allows user to retrieve a link to then reset the password
     */
    public function password_recovery($recovery_hash = null) {
        $this->grantAccess('all');
        
        if(!is_null($recovery_hash)) {
            $this->Identity->contain();
            $identity = $this->Identity->find(
                'first',
                array(
                    'conditions' => array(
                        'password_recovery_hash' => $recovery_hash                        
                    )
                )
            );
            if(strlen($recovery_hash) != 32 || !$identity) {
                $this->flashMessage('alert', __('The password recovery link is not valid!', true));
            } else {
                $this->set('recovery_hash', $recovery_hash);
                $this->render('password_recovery_password');
                return;
            }
        } else if($this->data) {
            $conditions = array('network_id' => Context::NetworkId());
            if($this->data['Identity']['username']) {
                $splitted = $this->Identity->splitUsername($this->data['Identity']['username']);
                $conditions['Identity.username'] = $splitted['username'];
            }

            if($this->data['Identity']['email']) {
                $conditions['Identity.email'] = $this->data['Identity']['email'];
            }
            $this->Identity->contain();
            $identity = $this->Identity->find(
                'first',
                array(
                    'conditions' => $conditions
                )
            );
            
            if(!$identity) {
                $this->flashMessage('alert', __('The account could not be found!', true));
            } else {
                $email = $identity['Identity']['email'];
                if(!$email) {
                    $this->flashMessage('alert', __('No email address found!', true));
                } else {
                    App::import('model', 'Mail');
                    $Mail = new Mail;
                    $Mail->passwordRecovery($identity['Identity']['id']);
                    
                    $this->flashMessage('success', __('Please look into your inbox for the password recovery email.', true));
                }
            }
            
        }
    }
    
    /**
     * this is where the user actually sets the new password
     *
     * todo: use validation
     */
    public function password_recovery_set($recovery_hash = null) {
        if(is_null($recovery_hash)) {
            $this->flashMessage('alert', __('Something went wrong. Please try again!', true));
            $this->redirect($this->referer());
        }
        $this->Identity->contain();
        $identity = $this->Identity->find(
            'first',
            array(
                'conditions' => array(
                    'password_recovery_hash' => $recovery_hash                        
                ),
                'fields' => array(
                    'id'
                )
            )
        );
        if(strlen($recovery_hash) != 32 || !$identity) {
            $this->flashMessage('alert', __('The password recovery link is not valid!', true));
            $this->redirect($this->referer());
        } else if(!$this->data) {
            $this->flashMessage('alert', __('Something went wrong. Please try again!', true));
            $this->redirect($this->referer());
        } else if($this->data['Identity']['password'] != $this->data['Identity']['password2']) {
            $this->flashMessage('alert', __('The two passwords were not the same!', true));
            $this->redirect($this->referer());
        } else if(strlen($this->data['Identity']['password']) < 6) {
            $this->flashMessage('alert', __('The password is too short!', true));
            $this->redirect($this->referer());
        } else {
            $this->Identity->id = $identity['Identity']['id'];
            $this->Identity->saveField('password', md5($this->data['Identity']['password']));
            $this->Identity->saveField('hash', ''); # user also verified email address with this method
            $this->Identity->saveField('password_recovery_hash', '');
            $this->flashMessage('success', __('You now can log in with your new password.', true));
            $this->redirect('/pages/login/');
        }
    }
}