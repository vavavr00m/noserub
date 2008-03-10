<?php
class LocationsController extends AppController {
    var $uses = array('Location');
    var $helpers = array('form', 'flashmessage');
    var $components = array('url', 'geocoder', 'api');
    
    public function index() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        if ($this->data) {
        	$this->ensureSecurityToken();
        	$this->data['Identity']['id'] = $session_identity['id'];
        	
        	if (!isset($this->data['Identity']['api_active'])) {
        		$this->data['Identity']['api_active'] = false;
        	}
        	
        	$this->Location->Identity->save($this->data, false, array('api_hash', 'api_active'));
        	$this->Session->write('Identity.api_hash', $this->data['Identity']['api_hash']);
        	$this->Session->write('Identity.api_active', $this->data['Identity']['api_active']);
        	$session_identity = $this->Session->read('Identity');
        	$this->flashMessage('success', 'API options have been saved.');
        }
        
        $this->Location->recursive = 0;
        $this->Location->expects('Location');
        $data = $this->Location->findAllByIdentityId($session_identity['id']);
        
        $this->set('data', $data);
        $this->set('session_identity', $session_identity);
        $this->set('headline', 'Manage your locations');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function add() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        if($this->data) {
            if($this->data['Location']['name']) {
                if($this->data['Location']['address']) {
                    $geolocation = $this->geocoder->get($this->data['Location']['address']);
                    if($geolocation !== false) {
                        $this->data['Location']['latitude']  = $geolocation['latitude'];
                        $this->data['Location']['longitude'] = $geolocation['longitude'];
                    } else {
                        $this->data['Location']['latitude']  = 0;
                        $this->data['Location']['longitude'] = 0;
                    }
                }
                $this->data['Location']['identity_id'] = $session_identity['id'];
                $this->Location->create();
                if($this->Location->save($this->data)) {
                    $this->flashMessage('success', 'Location added.');
                    $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/locations/');
                	$this->redirect($url, null, true);
                } else {
                    $this->flashMessage('error', 'Location could not be created.');
                }
            } else {
                $this->Location->invalidate('name');
            }
        } 
        
        $this->set('headline', 'Add new Location');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function edit() {
        $location_id = isset($this->params['location_id']) ? $this->params['location_id'] :  0;
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted    = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] ||
           !$location_id) {
            # this is not the logged in user, or location_id not set
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        # get the location and check, if it is this user's location
            $this->Location->recursive = 0;
            $this->Location->expects('Location');
            $location = $this->Location->find(array('id' => $location_id, 'identity_id' => $session_identity['id']));
            if(!$location) {
                $this->flashMessage('error', 'Location could not be edited.');
                $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/locations/');
            	$this->redirect($url, null, true);
            }
            
        if($this->data) {
            if($this->data['Location']['name']) {
                if($this->data['Location']['address']) {
                    $geolocation = $this->geocoder->get($this->data['Location']['address']);
                    if($geolocation !== false) {
                        $this->data['Location']['latitude']  = $geolocation['latitude'];
                        $this->data['Location']['longitude'] = $geolocation['longitude'];
                    } else {
                        $this->data['Location']['latitude']  = 0;
                        $this->data['Location']['longitude'] = 0;
                    }
                }
                $this->Location->id = $location['Location']['id'];
                if($this->Location->save($this->data)) {
                    $this->flashMessage('success', 'Location added.');
                } else {
                    $this->flashMessage('error', 'Location could not be created.');
                }
                $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/locations/');
            	$this->redirect($url, null, true);
            } else {
                $this->Location->invalidate('name');
            }
        } else {
            $this->data = $location;
        }
        
        $this->set('headline', 'Edit Location');
        $this->render('add');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function delete() {
        $username    = isset($this->params['username'])    ? $this->params['username']    : '';
        $location_id = isset($this->params['location_id']) ? $this->params['location_id'] :  0;
        $splitted = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] ||
           $location_id == 0) {
            # this is not the logged in user, or invalid location_id
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # check, if the location_id belongs to the logged in user
        $this->Location->recursive = 0;
        $this->Location->expects('Location');
        if(1 == $this->Location->findCount(array('id' => $location_id, 'identity_id' => $session_identity['id']))) {
            # everything ok, we can delete now...
            $this->Location->delete($location_id);
            
            $this->flashMessage('success', 'Location deleted.');            
        } else {
            $this->flashMessage('error', 'Location could not be deleted.');
        }
        
        $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/locations/');
    	$this->redirect($url, null, true);
    }
    
    public function api_get() {
        $identity = $this->api->getIdentity();
        $this->api->exitWith404ErrorIfInvalid($identity);
        
        $this->Location->recursive = 0;
        $this->Location->expects('Location');
        $data = $this->Location->findAllByIdentityId($identity['Identity']['id'], array('id', 'name'));
        
        $this->Location->Identity->recursive = 0;
        $this->Location->Identity->id = $identity['Identity']['id'];
        $last_location_id = $this->Location->Identity->field('last_location_id');
        
        $this->set(
            'data', 
            array(
                'Locations' => $data,
                'Identity'  => array(
                    'last_location_id' => $last_location_id
                )
            )
        );
        
        $this->api->render();
    }
    
    public function api_set($location_id) {
        $identity = $this->api->getIdentity();
        $this->api->exitWith404ErrorIfInvalid($identity);
        
        if(!$this->Location->setTo($identity['Identity']['id'], $location_id)) {
            $this->set('code', -1);
            $this->set('msg', 'dataset not found');
        }
        
        $this->api->render();
    }
}