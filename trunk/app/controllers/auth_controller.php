<?php
	$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.VENDORS;
	$path = ini_get('include_path');
	$path = $pathExtra . PATH_SEPARATOR . $path;
	ini_set('include_path', $path);

	vendor('Auth/OpenID/Server', 'Auth/OpenID/FileStore');

	class AuthController extends AppController {
		var $uses = array();
		
		function index() {
			$server = $this->__getOpenIDServer();
			$request = $server->decodeRequest();
			
			if (!isset($request->mode)) {
				$this->set('headline', 'OpenID server endpoint');
				$this->render('server_endpoint');
			} else {
				if (get_class($request) == 'Auth_OpenID_CheckIDRequest') {
					$response = $request->answer(false);
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