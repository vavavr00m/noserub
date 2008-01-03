<?php
class LocationsController extends AppController {
    var $uses = array('Location');
    var $helpers = array('form', 'flashmessage');
    var $components = array('url', 'geocoder');
    
    public function index() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Location->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
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
}