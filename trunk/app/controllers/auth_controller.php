<?php
	$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.VENDORS;
	$path = ini_get('include_path');
	$path = $pathExtra . PATH_SEPARATOR . $path;
	ini_set('include_path', $path);

	vendor('Auth/OpenID/Server', 'Auth/OpenID/FileStore');

	class AuthController extends AppController {
		const SESSION_KEY_FOR_LAST_OPENID_REQUEST = 'Noserub.lastOpenIDRequest';
		var $uses = array();
		
		function index() {
			$request = $this->__getOpenIDRequest();

			if (!isset($request->mode)) {
				$this->set('filter', false);
				$this->set('headline', 'OpenID server endpoint');
				$this->render('server_endpoint');
			} else {
				if (get_class($request) == 'Auth_OpenID_CheckIDRequest') {
					if ($this->Session->check('Identity')) {
						$identity = $this->Session->read('Identity');
						
						$requestIdentity = str_replace('https://', '', $request->identity);
						$requestIdentity = str_replace('http://', '', $requestIdentity);
						
						if ($identity['username'] == $requestIdentity) {
							$this->Session->write(self::SESSION_KEY_FOR_LAST_OPENID_REQUEST, $request);
							$this->redirect('/auth/trust', null, true);
						} else {
							$response = $request->answer(false);
						}

						// TODO add support for sreg
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
			$sessionKey = self::SESSION_KEY_FOR_LAST_OPENID_REQUEST;
			
			if ($this->Session->check($sessionKey)) {
				$request = $this->Session->read($sessionKey);
				
				if (empty($this->params['form'])) {
					$this->set('trustRoot', $request->trust_root);
					$this->set('identity', $request->identity);
					$this->set('filter', false);
					$this->set('headline', 'OpenID verification');
				} else {
					$this->Session->delete($sessionKey);
					$answer = (isset($this->params['form']['Allow'])) ? true : false;
					$response = $request->answer($answer);
					$this->__renderResponse($response);
				}
			} else {
				$this->set('filter', false);
				$this->set('headline', 'Error');
				$this->render('no_request');
			}
		}
		
		function xrds() {
			$this->layout = 'xml';
			header('Content-type: application/xrds+xml');
			$this->set('server', Router::url('/'.low($this->name), true));
		}
		
		function __getOpenIDRequest() {
			$sessionKey = self::SESSION_KEY_FOR_LAST_OPENID_REQUEST;
			
			if ($this->Session->check($sessionKey)) {
				$request = $this->Session->read($sessionKey);
				$this->Session->delete($sessionKey);
			} else {
				$server = $this->__getOpenIDServer();
				$request = $server->decodeRequest();
			}
			
			return $request;
		}
		
		function __getOpenIDServer() {
			$store = new Auth_OpenID_FileStore(TMP.'openid');
			$server = new Auth_OpenID_Server($store);
			
			return $server;
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