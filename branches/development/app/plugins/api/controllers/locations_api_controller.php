<?php

class LocationsApiController extends ApiAppController {
	public $uses = array('Location');
	public $components = array('OauthServiceProvider');
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
}