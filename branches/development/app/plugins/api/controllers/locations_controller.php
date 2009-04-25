<?php

class LocationsController extends ApiAppController {
	public $uses = array('Location');
	public $components = array('Geocoder', 'OauthServiceProvider');
	private $identity_id = null;
	
	public function beforeFilter() {
		if (isset($this->params['username'])) {
    		$identity = $this->Api->getIdentity();
        	$this->Api->exitWith404ErrorIfInvalid($identity);
        	$this->identity_id = $identity['Identity']['id'];
		} else {
    		$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
			$accessToken = ClassRegistry::init('AccessToken');
			$this->identity_id = $accessToken->field('identity_id', array('token_key' => $key));
		}
	}
	
	public function add_location() {
        $name    = isset($this->params['url']['name'])    ? $this->params['url']['name']    : '';
        $address = isset($this->params['url']['address']) ? $this->params['url']['address'] : '';
        $set_to  = isset($this->params['url']['set_to'])  ? $this->params['url']['set_to']  :  0;
        
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
        
        $this->Api->render();
    }
	
	public function get_last_location() {
		$this->Location->Identity->id = $this->identity_id;
		$this->Location->Identity->contain('Location');
		
		$data = $this->Location->Identity->read();
        $this->set(
            'data', 
            array(
                'id'   => isset($data['Location']['id'])   ? $data['Location']['id']   : 0,
                'name' => isset($data['Location']['name']) ? $data['Location']['name'] : 0
            )
        );
        
        $this->Api->render();
	}
	
	public function get_locations() {
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
        
        $this->Api->render();
    }
    
	public function set_location($location_id) {
        if(!$this->Location->setTo($this->identity_id, $location_id)) {
            $this->set('code', -1);
            $this->set('msg', __('dataset not found', true));
        }
        
        $this->Api->render();
    }
}