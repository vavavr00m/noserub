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
        $this->Account->contain(array('ServiceType', 'Service'));
        $accounts = $this->Account->findAllByIdentityId($this->identity_id);

        $data = array();
        foreach($accounts as $item) {
            $data[] = array(
                'title' => $item['Account']['title'],
                'url'   => $item['Account']['account_url'],
                'icon'  => $item['Service']['icon'],
                'type'  => $item['ServiceType']['name']
            );
        }
        $this->set('data', $data);
        
        $this->Api->render();
    }
}