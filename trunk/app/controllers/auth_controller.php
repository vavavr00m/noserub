<?php
	$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.VENDORS;
	$path = ini_get('include_path');
	$path = $pathExtra . PATH_SEPARATOR . $path;
	ini_set('include_path', $path);

	vendor('Auth/OpenID/Server', 'Auth/OpenID/FileStore');

	class AuthController extends AppController {
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
						
						$answer = ($identity['username'] == $requestIdentity) ? true : false;
						$response = $request->answer($answer);
						// TODO add support for sreg
					} else {
						$this->Session->write('Noserub.lastOpenIDRequest', $request);
						$this->redirect('/pages/login/', null, true);
					}
				} else {
					$response = $server->handleRequest($request);
				}

				$this->__renderResponse($response);
			}
		}
		
		function xrds() {
			$this->layout = 'xml';
			header('Content-type: application/xrds+xml');
			$this->set('server', Router::url('/'.low($this->name), true));
		}
		
		function __getOpenIDRequest() {
			$sessionKey = 'Noserub.lastOpenIDRequest';
			
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