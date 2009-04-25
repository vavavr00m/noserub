<?php

class ContactsController extends ApiAppController {
	public $uses = array('Contact');
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
	
	public function get_contacts() {
        $this->Contact->contain(array('WithIdentity', 'NoserubContactType'));
        
        $conditions = array(
            'Contact.identity_id' => $this->identity_id,
            'WithIdentity.username NOT LIKE "%@%"'
        );
    	$data = $this->Contact->find('all', array('conditions' => $conditions,
    											  'order' => array('WithIdentity.created DESC')));   
        
        $contacts = array();
        foreach($data as $item) {
            $xfn = array();
            foreach($item['NoserubContactType'] as $nct) {
                if($nct['is_xfn']) {
                    $xfn[] = $nct['name'];
                }
            }
            if(!$xfn) {
                $xfn[] = 'contact';
            }
            
            $contact = array(
                'url' => 'http://' . $item['WithIdentity']['username'],
                'firstname' => $item['WithIdentity']['firstname'],
                'lastname'  => $item['WithIdentity']['lastname'],
                'photo'     => $this->Contact->Identity->getPhotoUrl($item, 'WithIdentity', true),
                'xfn'       => join(' ', $xfn)
            );
            $contacts[] = $contact;
        }
        
        $this->set('data', $contacts);
        $this->Api->render();
    }
}