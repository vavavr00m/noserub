<?php
class LocationsController extends AppController {
    public $uses = array('Location');
    public $helpers = array('form', 'flashmessage');
    public $components = array('url', 'geocoder', 'api', 'OauthServiceProvider');
    
    public function index() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        $this->Location->contain();
        $data = $this->Location->findAllByIdentityId($session_identity['id']);
        
        $this->set('data', $data);
        $this->set('session_identity', $session_identity);
        $this->set('headline', 'Manage your locations');
    }
    
    public function add() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
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
                	$this->redirect($url);
                } else {
                    $this->flashMessage('error', 'Location could not be created.');
                }
            } else {
                $this->Location->invalidate('name');
            }
        } 
        
        $this->set('headline', 'Add new Location');
    }
    
    public function edit() {
        $location_id = isset($this->params['location_id']) ? $this->params['location_id'] :  0;
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted    = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] ||
           !$location_id) {
            # this is not the logged in user, or location_id not set
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        # get the location and check, if it is this user's location
        $this->Location->contain();
        $location = $this->Location->find(array('id' => $location_id, 'identity_id' => $session_identity['id']));
        if(!$location) {
            $this->flashMessage('error', 'Location could not be edited.');
            $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/locations/');
            $this->redirect($url);
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
            	$this->redirect($url);
            } else {
                $this->Location->invalidate('name');
            }
        } else {
            $this->data = $location;
        }
        
        $this->set('headline', 'Edit Location');
        $this->render('add');
    }
    
    public function delete() {
        $username    = isset($this->params['username'])    ? $this->params['username']    : '';
        $location_id = isset($this->params['location_id']) ? $this->params['location_id'] :  0;
        $splitted = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] ||
           $location_id == 0) {
            # this is not the logged in user, or invalid location_id
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # check, if the location_id belongs to the logged in user
        if($this->Location->hasAny(array('id' => $location_id, 'identity_id' => $session_identity['id']))) {
            # everything ok, we can delete now...
            $this->Location->delete($location_id);
            
            $this->flashMessage('success', 'Location deleted.');            
        } else {
            $this->flashMessage('error', 'Location could not be deleted.');
        }
        
        $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/locations/');
    	$this->redirect($url);
    }
    
    public function api_get() {
    	$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
    	$accessToken = ClassRegistry::init('AccessToken');
		$identity_id = $accessToken->field('identity_id', array('token_key' => $key));
    	
        $this->Location->contain();
        $data = $this->Location->findAllByIdentityId($identity_id, array('id', 'name'));
        
        $this->Location->Identity->recursive = 0;
        $this->Location->Identity->id = $identity_id;
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
    
    public function api_add() {
        $identity = $this->api->getIdentity();
        $this->api->exitWith404ErrorIfInvalid($identity);

        $name    = isset($this->params['url']['name'])    ? $this->params['url']['name']    : '';
        $address = isset($this->params['url']['address']) ? $this->params['url']['address'] : '';
        $set_to  = isset($this->params['url']['set_to'])  ? $this->params['url']['set_to']  :  0;
        
        if(!$name) {
            $this->set('code', -2);
            $this->set('msg', 'parameter wrong');
        } else {
            # test, wether we already have this location
            $conditions = array(
                'identity_id' => $identity['Identity']['id'],
                'name'        => $name
            );
            if($this->Location->hasAny($conditions)) {
                $this->set('code', -3);
                $this->set('msg', 'duplicate dataset');
            } else {
                $data = array(
                    'identity_id' => $identity['Identity']['id'],
                    'name'        => $name,
                    'address'     => $address
                );
                if($address) {
                    $geolocation = $this->geocoder->get($address);
                    if($geolocation !== false) {
                        $data['latitude']  = $geolocation['latitude'];
                        $data['longitude'] = $geolocation['longitude'];
                    }
                }
                $this->Location->create();
                $this->Location->save($data, true, array_keys($data));
                
                if($set_to == 1) {
                    $this->Location->cacheQueries = false;
                    $this->Location->setTo($identity['Identity']['id'], $this->Location->id);
                }
            }
        }
        
        $this->api->render();
    }
}