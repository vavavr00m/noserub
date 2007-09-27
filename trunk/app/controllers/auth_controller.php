<?php
	$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.VENDORS;
	$path = ini_get('include_path');
	$path = $pathExtra . PATH_SEPARATOR . $path;
	ini_set('include_path', $path);

	vendor('Auth/OpenID/Server', 'Auth/OpenID/FileStore', 'Auth/OpenID/SReg');

	class AuthController extends AppController {
		const SESSION_KEY_FOR_LAST_OPENID_REQUEST = 'Noserub.lastOpenIDRequest';
		const SESSION_KEY_FOR_AUTHENTICATED_OPENID_REQUEST = 'Noserub.authenticatedOpenIDRequest';
		var $uses = array();
		
		function index() {
			$server = $this->__getOpenIDServer();
			$request = $this->__getOpenIDRequest($server);
			
			if (!isset($request->mode)) {
				$this->set('headline', 'OpenID server endpoint');
				$this->render('server_endpoint');
			} else {
				if (get_class($request) == 'Auth_OpenID_CheckIDRequest') {
					if ($this->Session->check('Identity')) {
						$identity = $this->Session->read('Identity');
						
						$requestIdentity = str_replace('https://', '', $request->identity);
						$requestIdentity = str_replace('http://', '', $requestIdentity);
						
						if ($identity['username'] == $requestIdentity) {
							if ($request->immediate) {
								$response = $request->answer(true);
							} else {
								$this->Session->write(self::SESSION_KEY_FOR_AUTHENTICATED_OPENID_REQUEST, $request);
								$this->redirect('/auth/trust', null, true);
							}
						} else {
							$response = $request->answer(false);
						}
					} else {
						$this->Session->write(self::SESSION_KEY_FOR_LAST_OPENID_REQUEST, $request);
						$this->redirect('/pages/login/', null, true);
					}
				} else {
					$response = $server->handleRequest($request);
				}

				$this->__renderResponse($response);
			}
		}
		
		function trust() {
			$sessionKey = self::SESSION_KEY_FOR_AUTHENTICATED_OPENID_REQUEST;
			
			if ($this->Session->check($sessionKey)) {
				$request = $this->Session->read($sessionKey);
				$sregRequest = Auth_OpenID_SRegRequest::fromOpenIDRequest($request->message);
				
				if (empty($this->params['form'])) {
					$this->set('required', $this->__prepareSRegData($sregRequest->required));
					$this->set('optional', $this->__prepareSRegData($sregRequest->optional));
					
					$this->set('trustRoot', $request->trust_root);
					$this->set('identity', $request->identity);
					$this->set('headline', 'OpenID verification');
				} else {
					$this->Session->delete($sessionKey);
					$answer = (isset($this->params['form']['Allow'])) ? true : false;
					$response = $request->answer($answer);

					if ($answer) {
						$data = am($this->__prepareSRegData($sregRequest->required),
								   $this->__prepareSRegData($sregRequest->optional));						
						
						Auth_OpenID_sendSRegFields($request, $data, $response);
					}
					$this->__renderResponse($response);
				}
			} else {
				$this->set('headline', 'Error');
				$this->render('no_request');
			}
		}
		
		function xrds() {
			$this->layout = 'xml';
			header('Content-type: application/xrds+xml');
			$this->set('server', Router::url('/'.low($this->name), true));
		}
		
		function __getOpenIDRequest($server) {
			$sessionKey = self::SESSION_KEY_FOR_LAST_OPENID_REQUEST;
			
			if ($this->Session->check($sessionKey)) {
				$request = $this->Session->read($sessionKey);
				$this->Session->delete($sessionKey);
			} else {
				$request = $server->decodeRequest();
			}
			
			return $request;
		}
		
		function __getOpenIDServer() {
			$store = new Auth_OpenID_FileStore(TMP.'openid');
			$server = new Auth_OpenID_Server($store);
			
			return $server;
		}
		
		function __prepareSRegData($fields) {
			$result = array();
			$identity = $this->Session->read('Identity');
			
			// the fields are according to http://openid.net/specs/openid-simple-registration-extension-1_0.html
			foreach ($fields as $field) {
				switch ($field){
					case 'email':
						$result['email'] = $identity['email'];
						break;
					case 'fullname':
						$result['fullname'] = $identity['firstname'] . ' ' . $identity['lastname'];
						break;
					case 'gender':
						if ($identity['sex'] === '1') {
							$result['gender'] = 'F';
						} elseif ($identity['sex'] === '2') {
							$result['gender'] = 'M';
						}
						break;
					// these fields are not supported yet
					case 'nickname':
					case 'dob':
					case 'postcode':
					case 'country':
					case 'language':
					case 'timezone':
					default:
				}
			}
			
			return $result;
		}
		
		function __renderResponse($response) {
			$server = $this->__getOpenIDServer();
			$webResponse = $server->encodeResponse($response);

			if ($webResponse->code == 200) {
				echo $webResponse->body;
				exit;
			}

			$this->redirect($webResponse->headers['location'], null, true);
		}
	}
?>