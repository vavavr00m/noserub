<?php

class AccountsController extends ApiAppController {
	public $uses = array('Account');
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
	
	public function get_accounts() {
	    $accounts = $this->Account->find('all', array(
	        'contain' => false,
	        'conditions' => array(
	            'Account.identity_id' => $this->identity_id
	        )
	    ));

        $services = Configure::read('services.data');
        $service_types = Configure::read('service_types');
        
        $data = array();
        foreach($accounts as $item) {
            $data[] = array(
                'title' => $item['Account']['title'],
                'url'   => $item['Account']['account_url'],
                'icon'  => $services[$item['Account']['service']]['icon'],
                'type'  => $service_types[$item['Account']['service_type']]['name'],
            );
        }
        $this->set('data', $data);
        
        $this->Api->render();
    }
}