<?php
/* SVN FILE: $Id:$ */

// needed to unserialize the OpenID request data from the session to get the username
class Auth_OpenID_CheckIDRequest {}

class IdentitiesController extends AppController {
    public $uses = array('Identity');
    public $helpers = array('form', 'openid', 'nicetime', 'flashmessage');
    public $components = array('geocoder', 'url', 'cluster', 'openid', 'cdn', 'Cookie', 'api', 'OauthServiceProvider');
    
    /**
     * Displays profile page of an identity 
     */
    public function index() {
        $this->checkUnsecure();
        
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $username = $splitted['username'];
        
        $session_identity = $this->Session->read('Identity');

        # check, if we need to redirect. only, when the user is not
        # logged in.
        $this->Identity->contain();
        $identity = $this->Identity->findByUsername($username);        
        if(strpos($identity['Identity']['redirect_url'], 'http://')  === 0 ||
           strpos($identity['Identity']['redirect_url'], 'https://') === 0) {
            # there is a redirect set
            if($identity['Identity']['id'] != $session_identity['id']) {
                $this->redirect($identity['Identity']['redirect_url'], '301');
            } else {
                $this->flashMessage('info', __('There is a redirect URL given!', true));
                $this->redirect('http://' . $username . '/settings/account/');
            }
        }
        
        if($splitted['namespace'] !== '' && $splitted['namespace'] != $session_identity['local_username']) {
            # don't display local contacts to anyone else, but the owner
            $data = null;
        } else {
        	$this->Identity->contain(array('Location', 'Account', 'Account.Service', 'Account.ServiceType'));
            $data = $this->Identity->find(
                'first',
                array(
                    'conditions' => array(
                        'username'  => $username,
                        'is_local'  => 1,
                        'hash'      => ''
                    )
                )
            );
            if($data) {
                # get the status of relationship between the logged in
                # user and the profile we're watching
                $relationship_status = '';
                if($data['Identity']['id'] == $session_identity['id']) {
                    $relationship_status = 'self';
                } else {
                    $this->Identity->Contact->contain(array('ContactType', 'NoserubContactType'));
                	$contact = $this->Identity->Contact->find(
                        array(
                            'identity_id'      => $session_identity['id'],
                            'with_identity_id' => $data['Identity']['id']));
                    $relationship_status = $contact ? 'contact' : 'none';
                    $this->set('contact', $contact);
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
                usort($accounts, 'sort_accounts');
                $this->set('accounts', $accounts);
                $this->set('communications', $communications);
                
                # get contacts of the displayed profile
                $all_contacts = $this->Identity->getContacts($data['Identity']['id']);

                $num_private_contacts = 0;
                $num_noserub_contacts = 0;
                foreach($all_contacts as $contact) {
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
                
                # now get all mutual contacts, when this is not the logged in user
                if($session_identity['id'] && $data['Identity']['id'] != $session_identity['id']) {
                	$mutual_contacts = $this->Identity->getMutualContacts($data['Identity']['id'], $session_identity['id'], 9);
                    if($mutual_contacts) {
                    	$this->set('mutual_contacts', $mutual_contacts);
                    }
                }
                
                # create $about_identity for the view
                $this->set('about_identity', $data['Identity']);
            }
        }
        
        $items = array();
        $filter = null;
        
        if($data) {
            if($splitted['username'] == $session_identity['username']) {
                $this->set('headline', __('My NoseRub Profile', true));
            } else {
                if($data['Identity']['latitude'] != 0 && $data['Identity']['longitude'] != 0 &&
                   $session_identity['latitude'] != 0 && $session_identity['longitude'] != 0) {
                    $this->set('distance', $this->geocoder->distance($data['Identity']['latitude'], $data['Identity']['longitude'],
                                                                     $session_identity['latitude'], $session_identity['longitude']));
                }
                $this->set('headline', $splitted['local_username'] . '\'s NoseRub page');
            }
            
            $filter = $this->getFilter($session_identity);

            # get last 100 items
            $conditions = array(
                'identity_id' => $data['Identity']['id'],
                'filter'      => $filter
            );
            $items = $this->Identity->Entry->getForDisplay($conditions, 50, false);
            if($items) {
                usort($items, 'sort_items');
                $items = $this->cluster->removeDuplicates($items);
                $items = $this->cluster->create($items);
            }
        } else {
        	header("HTTP/1.0 404 Not Found");
            $this->set('headline', __('Username could not be found!', true));
        }

        $this->set('base_url_for_avatars', $this->Identity->getBaseUrlForAvatars());
        $this->set('data', $data);
        $this->set('items', $items);
        $this->set('session_identity', $session_identity);
        $this->set('filter', $filter);
    }
    
    /**
     * Displays the social stream of the whole plattform.
     */
    public function social_stream() {
        $this->checkUnsecure();
    	header('X-XRDS-Location: http://'.$_SERVER['SERVER_NAME'].$this->webroot.'pages/yadis.xrdf');
    	
        $session_identity = $this->Session->read('Identity');
        $output = isset($this->params['output']) ? $this->params['output']   : 'html';

        $filter = $this->getFilter($session_identity);
        # get last 100 items
        $conditions = array(
            'filter'      => $filter
        );
        $items = $this->Identity->Entry->getForDisplay($conditions, 50);
        usort($items, 'sort_items');
        $items = $this->cluster->removeDuplicates($items);
        
        $identities = $this->Identity->getLastActive(9);
        
        if($output === 'html') {
            $items = $this->cluster->create($items);
        }
        
        # also get my contacts, when I'm logged in
        $logged_in_identity_id = $this->Session->read('Identity.id');
        if($logged_in_identity_id) {
        	$this->set('contacts', $this->Identity->getContacts($logged_in_identity_id, 9));
        }
        
        if($output === 'rss') {
            $this->set('filter', $filter);
            $this->set('data', $items);
            $this->set('syndication_name', __('Social Stream', true));
            $this->layout = 'feed_rss';
            $this->render('../syndications/feed');
        } else {
        	$this->set('base_url_for_avatars', $this->Identity->getBaseUrlForAvatars());
            $this->set('newbies', $this->Identity->getNewbies(9));
            $this->set('identities', $identities);
            $this->set('items', $items);
            $this->set('filter', $filter);
            $this->set('headline', __('All public social activities', true));
            $this->render('social_stream');
        }
    }
    
    public function favorites() {
        $this->checkUnsecure();
        
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $username = $splitted['username'];

        $this->Identity->contain();
        $identity = $this->Identity->findByUsername($username);
        
        $conditions = array(
            'favorited_by' => $identity['Identity']['id']
        );
        
        $items = $this->Identity->Entry->getForDisplay($conditions, 50, true);
        
        usort($items, 'sort_items');
        $items = $this->cluster->removeDuplicates($items);        
        $items = $this->cluster->create($items);
        
        $this->set('items', $items);
        $this->set('identity', $identity);
        $this->set('headline', sprintf(__("%s's favorites", true), $username));
    }
    
    public function send_message() {
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
                if(!mail($email, '['. NOSERUB_APP_NAME . '] ' . $clean_subject, $msg, 'From: ' . NOSERUB_EMAIL_FROM)) {
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
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
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
            	# check, if photo should be removed
                @unlink($path . $identity['Identity']['photo'] . '.jpg');
                @unlink($path . $identity['Identity']['photo'] . '-small.jpg');
                $identity['Identity']['photo'] = '';
                $this->data['Identity']['photo'] = $identity['Identity']['photo'];
            } else if(isset($this->data['Identity']['use_gravatar']) && 
                      $this->data['Identity']['use_gravatar'] == 1 && 
                      $this->data['Identity']['photo']['error'] == 4) {
            	# use gravatar image
            	$md5 = md5($identity['Identity']['email']);
            	$this->data['Identity']['photo'] = 'http://gravatar.com/avatar/' . $md5;
            	$this->Identity->Entry->addPhotoChanged($identity['Identity']['id'], null);
            } else if($this->data['Identity']['photo']['error'] != 0) {
            	# save the photo, if neccessary
                $this->data['Identity']['photo'] = $identity['Identity']['photo'];
            } else {                
                $this->Identity->id = $session_identity['id'];
                $filename = $this->Identity->uploadPhotoByForm($this->data['Identity']['photo']);
                if($filename) {
                    $this->data['Identity']['photo'] = $filename;
                    if(NOSERUB_USE_CDN) {
                        # store to CDN
                        $this->cdn->copyTo(AVATAR_DIR . $filename . '.jpg',       'avatars/'.$filename.'.jpg');
                        $this->cdn->copyTo(AVATAR_DIR . $filename . '-small.jpg', 'avatars/'.$filename.'-small.jpg');
                    }
                    $this->Identity->Entry->addPhotoChanged($identity['Identity']['id'], null);
                }
            }   
             
            $saveable = array('firstname', 'lastname', 'about', 'sex', 'photo', 'address', 'address_shown', 'latitude', 'longitude', 'modified');
            
            $this->Identity->id = $session_identity['id'];
            $this->Identity->save($this->data, false, $saveable);
            
            $this->flashMessage('success', __('Changes have been saved.', true));
            
        } else {
            $this->Identity->contain();
            $this->data = $this->Identity->findById($session_identity['id']);
        }
        
        $this->set('headline', __('Settings for my NoseRub Account', true));
    }
    
    public function display_settings() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();

            # sanitize all the filters
            $sanitized_filters = array();
            foreach($this->data['Identity']['overview_filters'] as $filter) {
                if($this->Identity->Account->ServiceType->sanitizeFilter($filter)) {
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
        
        $this->set('filters', $this->Identity->Account->ServiceType->getFilters());
        $this->set('headline', __('Configure your display options', true));
    }
    
    public function privacy_settings() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();
            
            # get the frontpage update, so we know if anything changed
            $this->Identity->id = $session_identity['id'];
            $frontpage_updates = $this->Identity->field('frontpage_updates');
            
            if($frontpage_updates != $this->data['Identity']['frontpage_updates']) {
                $this->Identity->Entry->updateRestriction($session_identity['id'], $frontpage_updates == 1 ? 1 : 0);
            }
            # ssave all settings
            $saveable = array('frontpage_updates', 'allow_emails');
            $this->Identity->save($this->data, true, $saveable);
            
            $this->flashMessage('success', __('Privacy settings have been saved.', true));
        } else {
            $this->Identity->id = $session_identity['id'];
            $this->data['Identity']['frontpage_updates'] = $this->Identity->field('frontpage_updates');
            $this->data['Identity']['allow_emails']      = $this->Identity->field('allow_emails');
        }
        
        $this->set('headline', __('Your privacy settings', true));
    }
    
    public function password_settings() {
        $this->checkSecure();
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] ) {
            # this is not the logged in user or the user used an OpenID to register
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        if($this->data) {
            # make sure, that the correct security token is set
            $this->ensureSecurityToken();

            # test, if this is about changing password, or api setting
            if(isset($this->params['form']['api'])) {
                 $this->data['Identity']['id'] = $session_identity['id'];

              	if(!isset($this->data['Identity']['api_active'])) {
              		$this->data['Identity']['api_active'] = false;
              	}

              	$this->Identity->save($this->data, false, array('api_hash', 'api_active'));
              	$this->Session->write('Identity.api_hash', $this->data['Identity']['api_hash']);
              	$this->Session->write('Identity.api_active', $this->data['Identity']['api_active']);
              	$session_identity = $this->Session->read('Identity');
              	$this->flashMessage('success', __('API options have been saved.', true));
            } else {
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
        }
        
        $this->set('session_identity', $session_identity);
        $this->set('headline', __('Password settings', true));
    }

    public function login() {
    	$this->checkSecure();
    	$sessionKeyForOpenIDRequest = 'Noserub.lastOpenIDRequest';
    	
    	if (!empty($this->data) || count($this->params['url']) > 1) {
    		if (isset($this->data['Identity']['username'])) {
    			$identity = $this->Identity->check($this->data);
    		} else {
    			$identity = $this->loginWithOpenID();
    		}
    		
    		if($identity) {
                $this->Session->write('Identity', $identity['Identity']);
                if ($this->Session->check($sessionKeyForOpenIDRequest)) {
                	$this->redirect('/auth');
                } else {
                    # check, if we should remember this user
                    if($this->data['Identity']['remember'] == 1) {
                        $this->Cookie->write('li', $identity['Identity']['id'], true, '4 weeks');
                    } 
                    
                    if (!$this->Session->check('Login.success_url')) {
	                    $this->flashMessage('success', __('Welcome! It\'s nice to have you back.', true));
	                    $url = $this->url->http('/' . urlencode(strtolower($identity['Identity']['local_username'])) . '/network/');
                    } else {
                    	$url = $this->url->http($this->Session->read('Login.success_url'));
                    	$this->Session->delete('Login.success_url');
                    }
                    
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
    
    // this method hides the fact that two requests are necessary when login 
    // with an OpenID 
    private function loginWithOpenID() {
    	$protocol = 'http://';
    	
    	if (NOSERUB_USE_SSL) {
    		$protocol = 'https://';
    	}
    	
    	$returnTo = $protocol.$_SERVER['SERVER_NAME'].$this->webroot.'pages/login';
    	
    	if (!empty($this->data)) {
    		$this->Session->write('OpenidLogin.remember', $this->data['Identity']['remember']);
    		$this->authenticateOpenID($this->data['Identity']['openid'], $returnTo);
    		exit;
    	} else {
    		$this->data['Identity']['remember'] = $this->Session->read('OpenidLogin.remember');
    		$this->Session->delete('OpenidLogin.remember');
    		$response = $this->getOpenIDResponseIfSuccess($returnTo);
    		return $this->Identity->checkOpenID($response);
    	}
    }
        
    public function logout() {
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # make sure the login cookie is invalid
        # set cookie
        $this->Cookie->del('li');
        
        $this->Session->delete('Identity');
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
        
        if($admin_hash != NOSERUB_ADMIN_HASH ||
           $admin_hash == '' ||
           !$identity_id) {
            # there is nothing to do for us here
            return false;
        }
        
        # see, if we can find the identity.
        # it should be in our database already.
        $this->Identity->contain();
        $identity = $this->Identity->findById($identity_id);

        if(!$identity || $identity['Identity']['is_local'] == 1) {
            # we could not find it, or this is a local identity
            return false;
        }
        
        $result = $this->Identity->sync($identity_id, $identity['Identity']['username']);
        if($result) {
            # check, if there is a new photo
            $this->Identity->id = $identity_id;
            $this->recursive = 0;
            $data = $this->Identity->read();
            if($data['Identity']['photo'] && strpos($data['Identity']['photo'], 'ttp://') > 0) {
                $filename = $this->Identity->uploadPhotoByUrl($data['Identity']['photo']);
                if(NOSERUB_USE_CDN) {
                    # store to CDN
                    $this->cdn->copyTo(AVATAR_DIR . $filename . '.jpg',       'avatars/' . $filename . '.jpg');
                    $this->cdn->copyTo(AVATAR_DIR . $filename . '-small.jpg', 'avatars/' . $filename . '-small.jpg');
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
        
        if($admin_hash != NOSERUB_ADMIN_HASH ||
           $admin_hash == '') {
            # there is nothing to do for us here
            return false;
        }
        
        # get all not local identities
        $this->Identity->contain();
        $identities = $this->Identity->find('all', array('conditions' => array('is_local' => 0), 'order' => array('last_sync ASC')));
        $synced = array();
        foreach($identities as $identity) {
            $this->Identity->sync($identity['Identity']['id'], $identity['Identity']['username']);       
            $synced[] = $identity['Identity']['username'];
        }

        $this->set('data', $synced);
    }
    
    public function shell_sync_all() {
        $this->params['admin_hash'] = NOSERUB_ADMIN_HASH;
        $this->jobs_sync_all();
        $this->render('jobs_sync_all');
    }
    
    public function cron_sync_all() {
        $cron_hash= isset($this->params['cron_hash'])  ? $this->params['cron_hash'] : '';
        
        if($cron_hash != NOSERUB_CRON_HASH ||
           $cron_hash == '') {
            # there is nothing to do for us here
            $this->set('data', __('Value for NOSERUB_CRON_HASH from noserub.php does not match or is empty!', true));
            $this->render('jobs_sync_all');
            return;
        }
        
        $this->params['admin_hash'] = NOSERUB_ADMIN_HASH;
        $this->jobs_sync_all();
        $this->render('jobs_sync_all');
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
    
    private function authenticateOpenID($openid, $returnTo, $required = array(), $optional = array()) {
    	try {
    		$this->openid->authenticate($openid, 
    									$returnTo, 
    									FULL_BASE_URL,
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
    
    private function getFilter($session_identity) {
    	$filter = isset($this->params['filter']) ? $this->params['filter'] : '';
    	$filter = $this->Identity->Account->ServiceType->sanitizeFilter($filter);
    	
    	if($filter == '') {
        	$filter = isset($session_identity['overview_filters']) ? explode(',', $session_identity['overview_filters']) : $this->Identity->Account->ServiceType->getDefaultFilters();
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
    
    /**
     * returns a "vcard" of the authenticated user
     */
    public function api_get() {
    	if (isset($this->params['username'])) {
    		$identity = $this->api->getIdentity();
        	$this->api->exitWith404ErrorIfInvalid($identity);
        	$identity_id = $identity['Identity']['id'];
		} else {
    		$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
			$accessToken = ClassRegistry::init('AccessToken');
			$identity_id = $accessToken->field('identity_id', array('token_key' => $key));
		}
			
		$this->Identity->id = $identity_id; 
        $this->Identity->contain('Location');
        $data = $this->Identity->read();
        
        $this->set(
            'data', 
            array(
                'firstname'     => $data['Identity']['firstname'],
                'lastname'      => $data['Identity']['lastname'],
                'url'           => 'http://' . $data['Identity']['username'],
                'photo'         => $this->Identity->getPhotoUrl($data),
                'about'         => $data['Identity']['about'],
                'address'       => $data['Identity']['address_shown'],
                'last_location' => array(
                    'id'   => isset($data['Location']['id'])   ? $data['Location']['id']   : 0,
                    'name' => isset($data['Location']['name']) ? $data['Location']['name'] : 0
                )
            )
        );
        
        $this->api->render();
    }
    
    /**
     * returns information about the last location
     */
	public function api_get_last_location() {
		if(isset($this->params['username'])) {
    		$identity = $this->api->getIdentity();
        	$this->api->exitWith404ErrorIfInvalid($identity);
        	$identity_id = $identity['Identity']['id'];
		} else {
			$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
			$accessToken = ClassRegistry::init('AccessToken');
			$identity_id = $accessToken->field('identity_id', array('token_key' => $key));
		}
		
		$this->Identity->id = $identity_id;
		$this->Identity->contain('Location');
		
		$data = $this->Identity->read();
        $this->set(
            'data', 
            array(
                'id'   => isset($data['Location']['id'])   ? $data['Location']['id']   : 0,
                'name' => isset($data['Location']['name']) ? $data['Location']['name'] : 0
            )
        );
        
        $this->api->render();
	}
	
	/**
     * used to return number of registered, active users, and some other
     * values.
     */
    public function api_info() {
        if(defined('NOSERUB_API_INFO_ACTIVE') && NOSERUB_API_INFO_ACTIVE) {
            $this->Identity->contain();
            $conditions = array(
                'is_local' => 1,
                'email <>' => '',
                'hash'     => '',
                'NOT username LIKE "%@"'
            );
            App::import('Model', 'Admin');
            $Admin = new Admin;
            $restricted_hosts = defined('NOSERUB_REGISTRATION_RESTRICTED_HOSTS') ? NOSERUB_REGISTRATION_RESTRICTED_HOSTS : false;
            $data = array(
                'num_users' => $this->Identity->find('count', array('conditions' => $conditions)),
                'registration_type' => defined('NOSERUB_REGISTRATION_TYPE') ? NOSERUB_REGISTRATION_TYPE : 'unknown',
                'restricted_hosts'  => $restricted_hosts ? 'yes' : 'no',
                'migration' => $Admin->getCurrentMigration()
            );
        } else {
            $data = array();
        }
        $this->set('data', $data);
        $this->api->render();
    }
}