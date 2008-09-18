<?php

class RegistrationController extends AppController {
	public $uses = array('Identity');
	public $components = array('openid', 'url');
	public $helpers = array('form');
	
	public function register() {
		$session_identity = $this->Session->read('Identity');
        if($session_identity) {
            # this user is already logged in...
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        $this->checkSecure();
        
        if(NOSERUB_REGISTRATION_TYPE != 'all') {
            $url = $this->url->http('/');
            $this->redirect($url);
        }

        if(!empty($this->data)) {
            if($this->Identity->register($this->data)) {
                $url = $this->url->http('/pages/register/thanks/');
                $this->redirect($url);
            }
        } else {
            # set default value for this privacy setting
            $this->data = array('Identity' => array('frontpage_updates' => 1,
                                                    'allow_emails'      => 2));
        }

        $this->set('headline', 'Register a new NoseRub account');
	}
	
	public function register_with_openid_step_1() {
    	$this->set('headline', 'Register a new NoseRub account - Step 1/2');
		$returnTo = $this->webroot.'pages/register/withopenid';
		$sregFields = array('email', 'nickname');
    	
    	if (!empty($this->data)) {
    		$this->authenticateOpenID($this->data['Identity']['openid'], $returnTo, $sregFields);
    	} else {
    		if (count($this->params['url']) > 1) {
    			$response = $this->getOpenIDResponseIfSuccess($returnTo);

    			$identity = $this->Identity->checkOpenID($response);
    			
    			if ($identity) {
    				# already registered, so we perform a login
    				$this->Session->write('Identity', $identity['Identity']);
    				$url = $this->url->http('/' . urlencode(strtolower($identity['Identity']['local_username'])) . '/');
                	$this->redirect($url);
    			} else {
	    			$sregResponse = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
    				$sreg = $sregResponse->contents();
    				
    				$this->Session->write('Registration.openid', $response->identity_url);
    				$this->Session->write('Registration.openid_identity', $response->message->getArg('http://openid.net/signon/1.0', 'identity'));
					$this->Session->write('Registration.openid_server_url', $response->endpoint->server_url);
    				
					foreach ($sregFields as $sregField) {
						if (@$sreg[$sregField]) {
	    					$this->Session->write('Registration.'.$sregField, $sreg[$sregField]);
	    				}
					}
	
	    			$this->redirect('/pages/register/withopenid/step2');
    			}
    		}
    	}
    }
    
	public function register_with_openid_step_2() {
    	if (!$this->Session->check('Registration.openid')) {
    		$this->redirect('/pages/register/withopenid');
    	}
    	
    	$this->set('headline', 'Register a new NoseRub account - Step 2/2');

    	if (!empty($this->data)) {
    		$this->data['Identity']['openid'] = $this->Session->read('Registration.openid');
    		$this->data['Identity']['openid_identity'] = $this->Session->read('Registration.openid_identity');
    		$this->data['Identity']['openid_server_url'] = $this->Session->read('Registration.openid_server_url');
    		
    		if($this->Identity->register($this->data)) {
	    		$this->removeRegistrationDataFromSession();
    			$this->redirect('/pages/register/thanks/');
            }
    	} else {
    		if ($this->Session->check('Registration.email')) {
    			$this->data['Identity']['email'] = $this->Session->read('Registration.email');
    		}
    		
    		if ($this->Session->check('Registration.nickname')) {
    			$this->data['Identity']['username'] = $this->Session->read('Registration.nickname');
    		}
    		
    		$this->data['Identity']['frontpage_updates'] = 1;
    	}
    }
    
	public function register_thanks() {
        $this->set('headline', 'Thanks for your registration!');
    }
    
	public function verify() {
        $hash = isset($this->params['hash']) ? $this->params['hash'] : '';
        
        $this->set('verify_ok', $this->Identity->verify($hash));
        $this->set('headline', 'Verify your e-mail address');
    }
    
	private function authenticateOpenID($openid, $returnTo, $required = array(), $optional = array()) {
    	try {
    		$this->openid->authenticate($openid, 
    									'http://'.$_SERVER['SERVER_NAME'].$returnTo, 
    									$this->url->http('/'),
    									$required,
    									$optional);
    	} catch (InvalidArgumentException $e) {
    		$this->Identity->invalidate('openid', 'invalid_openid');
			$this->render();
			exit;
    	} catch (Exception $e) {
    		echo $e->getMessage();
    		exit();
    	}
    }
    
	private function getOpenIDResponseIfSuccess($returnTo) {
    	$response = $this->openid->getResponse('http://'.$_SERVER['SERVER_NAME'].$returnTo);
    			
    	if ($response->status == Auth_OpenID_CANCEL) {
    		$this->Identity->invalidate('openid', 'verification_cancelled');
    		$this->render();
    		exit;
    	} elseif ($response->status == Auth_OpenID_FAILURE) {
    		$this->Identity->invalidate('openid', 'openid_failure');
    		$this->set('errorMessage', $response->message);
    		$this->render();
    		exit;
    	} elseif ($response->status == Auth_OpenID_SUCCESS) {
    		return $response;
    	}
    }
    
	private function removeRegistrationDataFromSession() {
    	$this->Session->delete('Registration.openid');
	    $this->Session->delete('Registration.openid_identity');
	    $this->Session->delete('Registration.openid_server_url');
	    $this->Session->delete('Registration.email');
	    $this->Session->delete('Registration.nickname');
    }
}