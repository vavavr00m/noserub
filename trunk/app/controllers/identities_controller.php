<?php
/* SVN FILE: $Id:$ */

// needed to unserialize the OpenID request data from the session to get the username
class Auth_OpenID_CheckIDRequest {}

class IdentitiesController extends AppController {
    var $uses = array('Identity');
    var $helpers = array('form', 'openid', 'nicetime');
    var $components = array('geocoder', 'url', 'cluster');
    
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
                # get number of accounts and contacts
                # todo: do this with queries instead of going through arra<
                $this->set('num_accounts', count($data['Account']));
                $num_private_contacts = 0;
                $num_noserub_contacts = 0;
                foreach($data['Contact'] as $contact) {
                    if(strpos($contact['WithIdentity']['username'], '@') === false) {
                        $num_noserub_contacts++;
                    } else {
                        $num_private_contacts++;
                    }
                }
                $this->set('num_private_contacts', $num_private_contacts);
                $this->set('num_noserub_contacts', $num_noserub_contacts);
            
                # create $about_identity for the view
                $this->set('about_identity', $data['Identity']);
            
                # get the status of relationship between the two
                if($data['Identity']['id'] == $session_identity['id']) {
                    $this->set('relationship_status', 'self');
                } else {
                    $this->Identity->Contact->recursive = 0;
                    $this->Identity->Contact->expects('Contact');
                    $is_contact = 1 == $this->Identity->Contact->findCount(array('identity_id'      => $session_identity['id'],
                                                                                 'with_identity_id' => $data['Identity']['id']));
                    $this->set('relationship_status', $is_contact ? 'contact' : 'none');
                }
            }
        }
        
        if($data) {
            # expand all identities (domain, namespace, url)
            $data['Identity'] = array_merge($data['Identity'], $this->Identity->splitUsername($data['Identity']['username']));
            foreach($data['Contact'] as $key => $contact) {
                $data['Contact'][$key]['WithIdentity'] = array_merge($contact['WithIdentity'], $this->Identity->splitUsername($contact['WithIdentity']['username']));
            }
        
            if($splitted['username'] == $session_identity['username']) {
                $this->set('headline', 'My NoseRub page');
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
            # geocode the address
            $geolocation = $this->geocoder->get($this->data['Identity']['address']);
            if($geolocation !== false) {
                $this->data['Identity']['latitude']  = $geolocation['latitude'];
                $this->data['Identity']['longitude'] = $geolocation['longitude'];
            } else {
                $this->data['Identity']['latitude']  = 0;
                $this->data['Identity']['longitude'] = 0;
            }
            
            $saveable = array('firstname', 'lastname', 'sex', 'address', 'latitude', 'longitude', 'modified');
            
            $this->Identity->id = $session_identity['id'];
            $this->Identity->save($this->data, false, $saveable);
        } else {
            $this->Identity->recursive = 0;
            $this->Identity->expects('Identity');
            $this->data = $this->Identity->findById($session_identity['id']);
        }
        
        $this->set('headline', 'Settings for your NoseRub profile');
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
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
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
            $data['Identity']['username'] = $splitted['username'];
            $data['Identity']['password'] = $data['Identity']['passwd'];
            if($this->Identity->check($data)) {
                if($this->data['Identity']['confirm'] == 0) {
                    $this->set('confirm_error', 'In order to delete your account, please check the check box.');
                } else if($this->data['Identity']['confirm'] == 1) {
                    $identity_id = $session_identity['id'];
                    $this->Identity->Account->deleteByIdentityId($identity_id);
                    $this->Identity->Contact->deleteByIdentityId($identity_id, $session_identity['local_username']);
                    $this->Identity->block($identity_id);
                    $this->Session->delete('Identity');
                    $this->redirect('/pages/account/deleted/', null, true);
                }
            } else {
                $this->Identity->invalidate('passwd');
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
        }

        $this->set('headline', 'Register a new NoseRub account');
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
}