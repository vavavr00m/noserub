#!/usr/bin/php -q
<?php
/**
 * The commandline script to run the tests.
 *
 * Copyright (c) 2007, Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
*/
class TestTaskRunner {
	function __construct($args = array()) {
		set_time_limit(0);
		$this->initConstants();
		$argCount = count($args);
		
		if ($argCount == 1 || $this->isHelpParam($args[1])) {
			$this->showHelp();
		} else {
			$taskName = 'test';
			$params = null;
			
			$appPath = $this->getAppPath($args[1]);
			
			if ($appPath != false) {
				$this->defineAppConstants($appPath);
				$this->setHttpHost();
				$this->loadCoreFiles();
				$params = $this->prepareParams($args, 2);
			} else {
				echo "No entry in vendors/testsuite/apps.ini for " . $args[1] . "\n";
				exit();
			}
		
			$this->setRequestUri();
			$this->executeTask($taskName, $params);
		}
	}
	
	private function defineAppConstants($appPath) {
		$delimiter = strrpos($appPath, DS);
		$root = substr($appPath, 0, $delimiter);
		$appdir = substr($appPath, $delimiter + 1);

		define('ROOT', $root);
		define('APP_DIR', $appdir);
		define('APP_PATH', ROOT.DS.APP_DIR.DS);
		define('WWW_ROOT', 'webroot');
		
		
	}
	
	private function executeTask($taskName, $params) {
		$class = $this->getTaskClass($taskName);

		if ($class !== null) {
			$class->execute($params);
		} else {
			echo "Task not found: " . $taskName . "\n";
		}
	}
	
	private function getAppPath($appPathShortcut) {
		$iniFile = CORE_PATH.'vendors'.DS.'testsuite'.DS.'apps.ini';

		if (file_exists($iniFile)) {
			$appArray = $this->readConfigFile($iniFile);

			if (array_key_exists($appPathShortcut, $appArray)) {
				return $appArray[$appPathShortcut];
			}
		}

		return false;
	}
	
	private function getTaskClass($taskName) {
		$scriptDir = dirname(__FILE__);
		$taskPath = 'testsuite'.DS.$taskName.'_task.php';
		$fileExists = true;

		if (file_exists(VENDORS.$taskPath)) {
			require(VENDORS.$taskPath);
		} elseif (file_exists($scriptDir.DS.$taskPath)) {
			require($scriptDir.DS.$taskPath);
		} else {
			$fileExists = false;
		}

		if ($fileExists) {
			$className = $taskName.'Task';
			return new $className;
		}

		return null;
	}
	
	private function initConstants() {
		if (function_exists('ini_set')) {
			ini_set('display_errors', '1');
			ini_set('error_reporting', E_ALL);
			ini_set('html_errors', false);
			ini_set('implicit_flush', true);
			ini_set('max_execution_time', 60 * 5);
		}
		define('PHP5', (phpversion() >= 5));
		define('DS', DIRECTORY_SEPARATOR);
		define('CAKE_CORE_INCLUDE_PATH', dirname(dirname(dirname(__FILE__))));
		define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
	}
	
	private function isHelpParam($param) {
		return ($param == 'help' || $param == '--help');
	}
	
	private function loadCoreFiles() {
		$includes = array(
			CORE_PATH . 'cake' . DS . 'basics.php',
			CORE_PATH . 'cake' . DS . 'config' . DS . 'paths.php',
			CORE_PATH . 'cake' . DS . 'libs' . DS . 'object.php',
		 	CORE_PATH . 'cake' . DS . 'libs' . DS . 'inflector.php',
			CORE_PATH . 'cake' . DS . 'libs' . DS . 'configure.php',
			CORE_PATH . 'cake' . DS . 'libs' . DS . 'cache.php'
		);

		foreach ($includes as $inc) {
			if (!require($inc)) {
				echo ("Failed to load Cake core file ".$inc);
				return false;
			}
		}

		Configure::getInstance(file_exists(CONFIGS . 'bootstrap.php'));
		
		include_once APP_PATH . 'config' . DS . 'core.php';
		require CORE_PATH . 'cake' . DS . 'libs' . DS . 'class_registry.php';

		Configure::write('debug', 1);
	}
	
	private function prepareParams($originalParams, $elementsToRemove) {
		$params = $originalParams;

		for ($i = 0; $i < $elementsToRemove; $i++) {
			array_shift($params);
		}

		return $params;
	}
	
	private function readConfigFile($fileName) {
		$fileLineArray = file($fileName);

		foreach($fileLineArray as $fileLine) {
			$dataLine = trim($fileLine);

			$delimiter = strpos($dataLine, '=');

			if ($delimiter > 0) {
				$key = strtolower(trim(substr($dataLine, 0, $delimiter)));
				$value = trim(substr($dataLine, $delimiter + 1));
				$iniSetting[$key] = $value;
			}
		}

		return $iniSetting;
	}
	
	private function setHttpHost() {
		$configFile = APP_PATH.'config'.DS.'test-config.php';
		$httpHost = 'localhost';
		
		if (file_exists($configFile)) {
			require($configFile);
			$config = new TestConfig();
			$httpHost = $config->httpHost;
		}
		
		$_SERVER['HTTP_HOST'] = $httpHost;
	}
	
	private function setRequestUri() {
		// XXX I am not sure whether this will work, it is possible that it causes unwanted side-effects...
		$_SERVER['REQUEST_URI'] = '/';
	}
	
	private function showHelp() {
		echo "Usage: php test.php app-alias [param1, ...]\n";
	}
}

new TestTaskRunner($argv);
?>