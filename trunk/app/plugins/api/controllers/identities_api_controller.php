<?php

class IdentitiesApiController extends ApiAppController {
	public $uses = array('Identity');
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
	
	public function get_vcard() {
		$this->Identity->id = $this->identity_id; 
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
        
        $this->Api->render();
    }
}