<?php
/**
 * A simple OpenID consumer component for CakePHP.
 * 
 * Requires at least version 2.1.0 of PHP OpenID library from http://openidenabled.com/php-openid/
 * 
 * To make use of Email Address to URL Transformation (EAUT), you also need the
 * EAUT library: http://code.google.com/p/eaut/
 *
 * To use the MySQLStore, the following steps are required:
 * - get PEAR DB: http://pear.php.net/package/DB
 * - run the openid.sql script to create the required tables 
 * - use one of the following config settings when adding the component to the $components array of 
 * 	 your controller(s):
 *     public $components = array('Openid' => array('use_database' => true)); // uses the "default" database configuration
 *     public $components = array('Openid' => array('database_config' => 'name_of_database_config'));
 * 
 * Copyright (c) by Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class OpenidComponent extends Object {
	private $controller = null;
	private $importPrefix = '';
	private $useDatabase = false;
	private $databaseConfig = 'default';
	
	public function __construct() {
		parent::__construct();

		$pathToVendorsFolder = $this->getPathToVendorsFolderWithOpenIDLibrary();
		
		if ($pathToVendorsFolder == '') {
			exit('Unable to find the PHP OpenID library');
		}
		
		if ($this->isPathWithinPlugin($pathToVendorsFolder)) {
			$this->importPrefix = $this->getPluginName() . '.';
		}
		
		$this->addToIncludePath($pathToVendorsFolder);
		$this->importCoreFilesFromOpenIDLibrary();
	}
	
	public function initialize($controller, $settings) {
		if (isset($settings['use_database'])) {
			$this->useDatabase = $settings['use_database'];
		}
		
		if (isset($settings['database_config'])) {
			$this->databaseConfig = $settings['database_config'];
			$this->useDatabase = true;
		}
	}
	
	public function startUp($controller) {
		$this->controller = $controller;
	}
	
	/**
	 * @throws InvalidArgumentException if an invalid OpenID was provided
	 */
	public function authenticate($openidUrl, $returnTo, $realm, $required = array(), $optional = array()) {
		if (trim($openidUrl) != '') {
			if ($this->isEmail($openidUrl)) {
				$openidUrl = $this->transformEmailToOpenID($openidUrl);
			}

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
			$redirectUrl = $authRequest->redirectUrl($realm, $returnTo);
			
			if (Auth_OpenID::isFailure($redirectUrl)) {
				throw new Exception('Could not redirect to server: '.$redirectUrl->message);
			} else {
				$this->controller->redirect($redirectUrl);
			}
		} else {
			$formId = 'openid_message';
			$formHtml = $authRequest->formMarkup($realm, $returnTo, false , array('id' => $formId));

			if (Auth_OpenID::isFailure($formHtml)) {
				throw new Exception('Could not redirect to server: '.$formHtml->message);
			} else {
				echo '<html><head><title>' . __('OpenID Authentication Redirect', true) . '</title></head>'.
					 "<body onload='document.getElementById(\"".$formId."\").submit()'>".
					 $formHtml.'</body></html>';
				exit;
			}
		}
	}
	
	/**
	 * Removes expired associations and nonces. 
	 *
	 * This method is not called in the normal operation of the component. It provides a way
	 * for store admins to keep their storage from filling up with expired data.
	 */
	public function cleanup() {
		$store = $this->getStore();

		return $store->cleanup();
	}

	public function getResponse($currentUrl) {
		$consumer = $this->getConsumer();
		$response = $consumer->complete($currentUrl, $this->getQuery());
		
		return $response;
	}
	
	public function isOpenIDResponse() {
		if (count($_GET) > 1 && isset($this->controller->params['url']['openid_ns'])) {
			return true;
		}
		
		return false;
	}
	
	private function addToIncludePath($pathToVendorsFolder) {
		$pathExtra = $pathToVendorsFolder . PATH_SEPARATOR . $pathToVendorsFolder . 'pear' . DS;
		$path = ini_get('include_path');
		$path = $pathExtra . PATH_SEPARATOR . $path;
		ini_set('include_path', $path);
	}
	
	private function getConsumer() {
		return new Auth_OpenID_Consumer($this->getStore());
	}

	private function getFileStore() {
		App::import('Vendor', $this->importPrefix.'filestore', array('file' => 'Auth'.DS.'OpenID'.DS.'FileStore.php'));
		$storePath = TMP.'openid';

		if (!file_exists($storePath) && !mkdir($storePath)) {
		    throw new Exception('Could not create the FileStore directory '.$storePath.'. Please check the effective permissions.');
		}
	
		return new Auth_OpenID_FileStore($storePath);
	}
	
	private function getMySQLStore() {
		App::import('Vendor', $this->importPrefix.'mysqlstore', array('file' => 'Auth'.DS.'OpenID'.DS.'MySQLStore.php'));
		$dataSource = ConnectionManager::getDataSource($this->databaseConfig);
			
		$dsn = array(
	    	'phptype'  => 'mysql',
	    	'username' => $dataSource->config['login'],
	    	'password' => $dataSource->config['password'],
	    	'hostspec' => $dataSource->config['host'],
	    	'database' => $dataSource->config['database'],
			'port'     => $dataSource->config['port']
		);

		$db = DB::connect($dsn);
		if (PEAR::isError($db)) {
		    die($db->getMessage());
		}

		return new Auth_OpenID_MySQLStore($db);
	}
	
	private function getPathToVendorsFolderWithOpenIDLibrary() {
		$pathToVendorsFolder = '';
		
		if ($this->isPathWithinPlugin(__FILE__)) {
			$pluginName = $this->getPluginName();
			
			if (file_exists(APP.'plugins'.DS.$pluginName.DS.'vendors'.DS.'Auth')) {
				$pathToVendorsFolder = APP.'plugins'.DS.$pluginName.DS.'vendors'.DS;
			}
		}

		if ($pathToVendorsFolder == '') {
			if (file_exists(APP.'vendors'.DS.'Auth')) {
				$pathToVendorsFolder = APP.'vendors'.DS;
			} elseif (file_exists(VENDORS.'Auth')) {
				$pathToVendorsFolder = VENDORS;
			}
		}
		
		return $pathToVendorsFolder;
	}
	
	private function getPluginName() {
		$result = array();
		if (preg_match('#'.DS.'plugins'.DS.'(.*)'.DS.'controllers#', __FILE__, $result)) { 
			return $result[1];
		}
		
		return false;
	}
	
	private function getQuery() {
		$query = Auth_OpenID::getQuery();
		
		// unset the url parameter automatically added by app/webroot/.htaccess 
		// as it causes problems with the verification of the return_to url
    	unset($query['url']);
    	
    	return $query;
	}
	
	private function getStore() {
		$store = null;
		
		if ($this->useDatabase) { 
			$store = $this->getMySQLStore();
		} else {	
			$store = $this->getFileStore();
		}
		
		return $store;
	}
	
	private function importCoreFilesFromOpenIDLibrary() {
		App::import('Vendor', $this->importPrefix.'consumer', array('file' => 'Auth'.DS.'OpenID'.DS.'Consumer.php'));
		App::import('Vendor', $this->importPrefix.'sreg', array('file' => 'Auth'.DS.'OpenID'.DS.'SReg.php'));
	}
	
	private function isEmail($string) {
		return strpos($string, '@');
	}
	
	private function isPathWithinPlugin($path) {
		return strpos($path, DS.'plugins'.DS) ? true : false;
	}
	
	private function transformEmailToOpenID($email) {
		if (App::import('Vendor', $this->importPrefix.'emailtoid', array('file' => 'Auth'.DS.'Yadis'.DS.'Email.php'))) {
			return Auth_Yadis_Email_getID($email);
		}
		
		throw new InvalidArgumentException('Invalid OpenID');
	}
}