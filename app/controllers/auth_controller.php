<?php
$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.VENDORS;
$path = ini_get('include_path');
$path = $pathExtra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);

App::import('Vendor', 'server', array('file' => 'Auth'.DS.'OpenID'.DS.'Server.php'));
App::import('Vendor', 'filestore', array('file' => 'Auth'.DS.'OpenID'.DS.'FileStore.php'));
App::import('Vendor', 'sreg', array('file' => 'Auth'.DS.'OpenID'.DS.'SReg.php'));

class AuthController extends AppController {
	const SESSION_KEY_FOR_LAST_OPENID_REQUEST = 'Noserub.lastOpenIDRequest';
	const SESSION_KEY_FOR_AUTHENTICATED_OPENID_REQUEST = 'Noserub.authenticatedOpenIDRequest';
	const OPENID_ENDPOINT_URL = '/auth';
	public $uses = array('OpenidSite');
	public $helpers = array('Nicesreg');
	
	public function index() {
		$server = $this->getOpenIDServer();
		$request = $this->getOpenIDRequest($server);

		if (!isset($request->mode)) {
			$this->set('headline', 'OpenID server endpoint');
			$this->render('server_endpoint');
		} else {
			if (get_class($request) == 'Auth_OpenID_CheckIDRequest') {
				if ($this->Session->check('Identity')) {
					$identity = $this->Session->read('Identity');
					
					$requestIdentity = str_replace('https://', '', $request->identity);
					$requestIdentity = str_replace('http://', '', $requestIdentity);
					$requestIdentity = rtrim($requestIdentity, '/');
					
					if ($identity['username'] == $requestIdentity) {
						if ($request->immediate) {
							$response = $request->answer(true, FULL_BASE_URL . self::OPENID_ENDPOINT_URL);
						} else {
							$this->Session->write(self::SESSION_KEY_FOR_AUTHENTICATED_OPENID_REQUEST, $request);
							$this->redirect('/auth/trust');
						}
					} else {
						$response = $request->answer(false);
					}
				} else {
					if ($request->immediate) {
						$response = $request->answer(false, FULL_BASE_URL . self::OPENID_ENDPOINT_URL);
					} else {
						$this->Session->write(self::SESSION_KEY_FOR_LAST_OPENID_REQUEST, $request);
						$this->redirect('/pages/login/');
					}
				}
			} else {
				$response = $server->handleRequest($request);
			}

			$this->renderResponse($response);
		}
	}
	
	public function trust() {
		$sessionKey = self::SESSION_KEY_FOR_AUTHENTICATED_OPENID_REQUEST;
		
		if ($this->Session->check($sessionKey)) {
			$request = $this->Session->read($sessionKey);
			$sregRequest = Auth_OpenID_SRegRequest::fromOpenIDRequest($request);
			$identity = $this->Session->read('Identity');
			
			$this->OpenidSite->contain();
			$openidSite = $this->OpenidSite->findByIdentityIdAndUrl($identity['id'], $request->trust_root);

			if (isset($openidSite['OpenidSite']['allowed']) && $openidSite['OpenidSite']['allowed']) {
				$this->Session->delete($sessionKey);
				$response = $request->answer(true, FULL_BASE_URL . self::OPENID_ENDPOINT_URL);
				$this->addSRegDataToResponse($response, $sregRequest, $openidSite);
				
				$this->renderResponse($response);
			} elseif(!empty($this->params['form'])) {
				$this->Session->delete($sessionKey);
				$answer = false;
				
				if (isset($this->params['form']['AllowForever']) || isset($this->params['form']['AllowOnce'])) {
					$answer = true;
					$this->data['OpenidSite']['id'] = (isset($openidSite['OpenidSite']['id'])) ? $openidSite['OpenidSite']['id'] : '';
					$this->data['OpenidSite']['url'] = $request->trust_root;
					$this->data['OpenidSite']['identity_id'] = $identity['id'];
					$this->data['OpenidSite']['allowed'] = (isset($this->params['form']['AllowForever'])) ? true : false;
					
					$this->OpenidSite->create($this->data);
					$this->OpenidSite->save();
				}

				$response = $request->answer($answer, FULL_BASE_URL . self::OPENID_ENDPOINT_URL);

				if ($answer) {
					$this->addSRegDataToResponse($response, $sregRequest, $this->data);
				}
				$this->renderResponse($response);
			} else {
				if ($openidSite) {
					$this->set('openidSite', $openidSite);
				}
				
				$this->setDataForTrustForm($request, $sregRequest);
			}				
		} else {
			$this->set('headline', 'Error');
			$this->render('no_request');
		}
	}
	
	public function xrds() {
		$this->layout = 'xml';
		header('Content-type: application/xrds+xml');
		$this->set('server', Router::url('/'.low($this->name), true));
	}
	
	private function addSRegDataToResponse($response, $sregRequest, $fields) {
		$data = am($this->prepareSRegData($this->removeUnwantedFields($sregRequest->required, $fields), true),
				   $this->prepareSRegData($this->removeUnwantedFields($sregRequest->optional, $fields), true));						

		$sregResponse = Auth_OpenID_SRegResponse::extractResponse($sregRequest, $data);
		$sregResponse->toMessage($response->fields);
	}
	
	private function getOpenIDRequest($server) {
		$sessionKey = self::SESSION_KEY_FOR_LAST_OPENID_REQUEST;
		
		if ($this->Session->check($sessionKey)) {
			$request = $this->Session->read($sessionKey);
			$this->Session->delete($sessionKey);
		} else {
			$request = $server->decodeRequest();
		}
		
		return $request;
	}
	
	private function getOpenIDServer() {
		$store = new Auth_OpenID_FileStore(TMP.'openid');
		$server = new Auth_OpenID_Server($store);
		
		return $server;
	}
	
	private function prepareSRegData($fields, $ignoreUnsupportedFields = false) {
		$result = array();
		$identity = $this->Session->read('Identity');
		
		// the fields are according to http://openid.net/specs/openid-simple-registration-extension-1_0.html
		foreach ($fields as $field) {
			switch ($field){
				case 'email':
					$result[$field] = $identity['email'];
					break;
				case 'fullname':
					$result[$field] = $identity['firstname'] . ' ' . $identity['lastname'];
					break;
				case 'gender':
					if ($identity['sex'] === '1') {
						$result[$field] = 'F';
					} elseif ($identity['sex'] === '2') {
						$result[$field] = 'M';
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
					if (!$ignoreUnsupportedFields) {
						$result[$field] = '';
					}
			}
		}
		
		return $result;
	}
	
	private function removeUnwantedFields($fields, $data) {
		$result = array();
		
		foreach ($fields as $field) {
			switch ($field) {
				case 'email':
				case 'fullname':
				case 'gender':
					if (isset($data['OpenidSite'][$field]) && $data['OpenidSite'][$field] === '1') {
						$result[] = $field;
					}
					
					break;
				default:
					$result[] = $field;
			}
		}
		
		return $result;
	}
	
	private function renderResponse($response) {
		$server = $this->getOpenIDServer();
		$webResponse = $server->encodeResponse($response);

		if ($webResponse->code == 200) {
			echo $webResponse->body;
			exit;
		}

		$this->redirect($webResponse->headers['location']);
	}
	
	private function setDataForTrustForm($request, $sregRequest) {
		$this->set('required', $this->prepareSRegData($sregRequest->required));
		$this->set('optional', $this->prepareSRegData($sregRequest->optional));
		$this->set('policyUrl', $sregRequest->policy_url);
		
		$this->set('trustRoot', $request->trust_root);
		$this->set('identity', $request->identity);
		$this->set('headline', 'OpenID verification');
	}
}