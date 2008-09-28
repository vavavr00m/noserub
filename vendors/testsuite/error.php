<?php
/**
 * Custom error handler.
 *
 * Copyright (c) 2007, Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version			$Revision$
 * @modifiedby		$LastChangedBy: dhofstet $
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

	define('SUCCESS', 0);
	define('REDIRECT', 1);
	define('ERROR', 2);
	define('ERROR_404', 3);
	define('MISSING_CONTROLLER', 4);
	define('MISSING_ACTION', 5);
	define('PRIVATE_ACTION', 6);
	define('MISSING_TABLE', 7);
	define('MISSING_DATABASE', 8);
	define('MISSING_VIEW', 9);
	define('MISSING_LAYOUT', 10);
	define('MISSING_CONNECTION', 11);
	define('MISSING_HELPER_FILE', 12); /* TODO does not work due to bug #1629 */
	define('MISSING_HELPER_CLASS', 13); /* TODO does not work due to bug #1629 */
	define('MISSING_COMPONENT_FILE', 14); /* TODO does not work due to bug #1629 */
	define('MISSING_COMPONENT_CLASS', 15); /* TODO does not work due to bug #1629 */
	define('MISSING_MODEL', 16);
	
	
	class ErrorHandler extends Object {
		
		function __construct($method, $messages) {
			parent::__construct();
			static $__previousError = null;
	
			$allow = array('.', '/', '_', ' ');
		    if(substr(PHP_OS,0,3) == "WIN") {
	            $allow = array_merge($allow, array('\\', ':') );
	        }
			$clean = new Sanitize();
			$messages = $clean->paranoid($messages, $allow);
			$this->__dispatch =& new Dispatcher();
	
			if ($__previousError != array($method, $messages)) {
				$__previousError = array($method, $messages);
	
				if (!class_exists('AppController')) {
					App::import('Controller', 'App');
				}
	
				$this->controller =& new AppController();
				$this->controller->_initComponents();
				$this->controller->cacheAction = false;
				$this->__dispatch->start($this->controller);
	
				if (method_exists($this->controller, 'apperror')) {
					return $this->controller->appError($method, $messages);
				}
			} else {
				$this->controller =& new Controller();
				$this->controller->cacheAction = false;
			}
			$helper =& new ErrorHandlerHelper();
			call_user_func_array(array(&$helper, $method), $messages);
		}
	}
	
	class ErrorHandlerHelper extends Object {
		var $error = null;
		
		function &getInstance() {
			static $instance = array();
			
			if (!isset($instance[0]) || !$instance[0]) {
				$instance[0] =& new ErrorHandlerHelper();
			}
	
			return $instance[0];
		}
		
		function error($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = ERROR;
		}
		
		function error404($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = ERROR_404;
		}
		
		function missingController($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_CONTROLLER;
		}
		
		function missingAction($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_ACTION;
		}
		
		function privateAction($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = PRIVATE_ACTION;
		}
		
		function missingTable($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_TABLE;
		}
		
		function missingDatabase($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_DATABASE;
		}
		
		function missingView($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_VIEW;
		}
		
		function missingLayout($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_LAYOUT;
		}
		
		function missingConnection($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_CONNECTION;
		}
		
		function missingHelperFile($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_HELPER_FILE;
		}
		
		function missingHelperClass($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_HELPER_CLASS;
		}
		
		function missingComponentFile($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_COMPONENT_FILE;
		}
		
		function missingComponentClass($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_COMPONENT_CLASS;
		}
		
		function missingModel($params) {
			$instance =& ErrorHandlerHelper::getInstance();
			$instance->error = MISSING_MODEL;
		}
	}
?>