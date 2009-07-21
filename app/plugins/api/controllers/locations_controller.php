<?php

class LocationsController extends ApiAppController {
	public $uses = array('Location');
	public $components = array('Geocoder', 'OauthServiceProvider');
	private $identity_id = null;
	
	public function beforeFilter() {
    	$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
		$accessToken = ClassRegistry::init('AccessToken');
		$this->identity_id = $accessToken->field('identity_id', array('token_key' => $key));
	}
	
	public function index() {
        $this->Location->contain();
        $data = $this->Location->findAllByIdentityId($this->identity_id, array('id', 'name'));
        
        $this->Location->Identity->recursive = 0;
        $this->Location->Identity->id = $this->identity_id;
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
    }
	
	public function add() {
		$name = $this->params['form']['name'];
		$address = $this->params['form']['address'];
		$set_to = $this->params['form']['set_to'];
        
        if(!$name) {
            $this->set('code', -2);
            $this->set('msg', __('parameter wrong', true));
        } else {
            # test, whether we already have this location
            $conditions = array(
                'identity_id' => $this->identity_id,
                'name'        => $name
            );
            if($this->Location->hasAny($conditions)) {
                $this->set('code', -3);
                $this->set('msg', __('duplicate dataset', true));
            } else {
                $data = array(
                    'identity_id' => $this->identity_id,
                    'name'        => $name,
                    'address'     => $address
                );
                if($address) {
                    $geolocation = $this->Geocoder->get($address);
                    if($geolocation !== false) {
                        $data['latitude']  = $geolocation['latitude'];
                        $data['longitude'] = $geolocation['longitude'];
                    }
                }
                $this->Location->create();
                $this->Location->save($data, true, array_keys($data));
                
                if($set_to == 1) {
                    $this->Location->cacheQueries = false;
                    $this->Location->setTo($identity_id, $this->Location->id);
                }
            }
        }

        // TODO just some fake return, need something better ;-)
        $this->set('data', array('ok' => 'done'));
    }
	
	public function current() {
		$this->Location->Identity->id = $this->identity_id;
		$this->Location->Identity->contain('Location');
		
		$data = $this->Location->Identity->read();
		
		if ($data['Location']['id'] != null) {
			$this->set('data', array('id' => $data['Location']['id'], 'name' => $data['Location']['name']));
		} else {
			$this->set('data', array());
		}
	}
    
	public function set_current() {
		if (isset($this->params['form']['location_id'])) {
			$location_id = $this->params['form']['location_id'];
	        if ($this->Location->setTo($this->identity_id, $location_id)) {
	        	$location = $this->Location->findById($location_id);
	        	$this->set('data', array('id' => $location_id, 'name' => $location['Location']['name']));
	        } else {
	            header("HTTP/1.1 400 Bad Request");
	            $this->set('data', array('error' => 'No location with id: ' . $location_id));
	        }
		} else {
			header("HTTP/1.1 400 Bad Request");
			$this->set('data', array('error' => 'Parameter location_id is missing'));
		}
    }
}