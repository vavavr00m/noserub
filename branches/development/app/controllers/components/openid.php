<?php
	$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.VENDORS;
	$path = ini_get('include_path');
	$path = $pathExtra . PATH_SEPARATOR . $path;
	ini_set('include_path', $path);
	
	vendor('Auth'.DS.'OpenID'.DS.'Consumer', 'Auth'.DS.'OpenID'.DS.'FileStore', 'Auth'.DS.'OpenID'.DS.'SReg');

	class OpenidComponent extends Object {
		private $controller = null;
		
		public function startUp($controller) {
			$this->controller = $controller;
		}
		
		/**
		 * @throws InvalidArgumentException if an invalid OpenID was provided
		 */
		public function authenticate($openidUrl, $returnTo, $trustRoot, $required = array(), $optional = array()) {
			if (trim($openidUrl) != '') {
				$consumer = $this->getConsumer();
				$authRequest = $consumer->begin($openidUrl);
			}
			
			if (!isset($authRequest) || !$authRequest) {
			    throw new InvalidArgumentException('Invalid OpenID');
			}
			
			$sregRequest = Auth_OpenID_SRegRequest::build($required, $optional);
			
			if ($sregRequest) {
				$authRequest->addExtension($sregRequest);
			}
			
			if ($authRequest->shouldSendRedirect()) {
				$redirectUrl = $authRequest->redirectUrl($trustRoot, $returnTo);
				
				if (Auth_OpenID::isFailure($redirectUrl)) {
					throw new Exception('Could not redirect to server: '.$redirectUrl->message);
				} else {
					$this->controller->redirect($redirectUrl, null, true);
				}
			} else {
				$formId = 'openid_message';
				$formHtml = $authRequest->formMarkup($trustRoot, $returnTo, false , array('id' => $formId));

				if (Auth_OpenID::isFailure($formHtml)) {
					throw new Exception('Could not redirect to server: '.$formHtml->message);
				} else {
					echo '<html><head><title>OpenID transaction in progress</title></head>'.
						 "<body onload='document.getElementById(\"".$formId."\").submit()'>".
						 $formHtml.'</body></html>';
					exit;
				}
			}
		}
		
		public function getResponse() {
			$consumer = $this->getConsumer();
			$response = $consumer->complete();
			
			return $response;
		}
		
		private function getConsumer() {
			$storePath = TMP.'openid';

			if (!file_exists($storePath) && !mkdir($storePath)) {
			    throw new Exception('Could not create the FileStore directory '.$storePath.'. Please check the effective permissions.');
			}

			$store = new Auth_OpenID_FileStore($storePath);
			$consumer = new Auth_OpenID_Consumer($store);
			
			return $consumer;
		}
	}
?>