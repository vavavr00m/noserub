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
	defineConstants();
	$cakeDir = CORE_PATH.DS.'cake'.DS;
	$argCount = count($argv);

	if ($argCount == 1 || isHelpParam($argv[1])) {
		showHelp();
	} else {
		$taskName = 'test';
		$params = null;

		$appPath = getAppPath($argv[1]);	

		if ($appPath != false) {
			defineAppConstants($appPath);
			$params = prepareParams($argv, 2);
		} else {
			echo "No entry in vendors/testsuite/apps.ini for " . $argv[1] . "\n";
			exit();
		}

		setHttpHost();
		includeCoreFiles($cakeDir);
		executeTask($taskName, $params);
	}

	function defineAppConstants($appPath) {
		$delimiter = strrpos($appPath, DS);
		$root = substr($appPath, 0, $delimiter);
		$appdir = substr($appPath, $delimiter + 1);

		define('ROOT', $root);
		define('APP_DIR', $appdir);
		define('APP_PATH', ROOT.DS.APP_DIR.DS);
		// TODO: how to handle situation with a non-standard webroot setup?
		define('WWW_ROOT', APP_PATH.'webroot'.DS);
	}

	function defineConstants() {
		define('PHP5', (phpversion() >= 5));
		define('DS', DIRECTORY_SEPARATOR);
		define('CAKE_CORE_INCLUDE_PATH', dirname(dirname(dirname(__FILE__))));
		define('CORE_PATH', CAKE_CORE_INCLUDE_PATH.DS);
	}

	function executeTask($taskName, $params) {
		$class = getTaskClass($taskName);

		if ($class !== null) {
			$class->execute($params);
		} else {
			echo "Task not found: " . $taskName . "\n";
		}
	}

	function getAppPath($appPathShortcut) {
		$iniFile = CORE_PATH.'vendors'.DS.'testsuite'.DS.'apps.ini';

		if (file_exists($iniFile)) {
			$appArray = readConfigFile($iniFile);

			if (array_key_exists($appPathShortcut, $appArray)) {
				return $appArray[$appPathShortcut];
			}
		}

		return false;
	}

	function getTaskClass($taskName) {
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

	function includeCoreFiles($cakePath) {
		require($cakePath.'basics.php');
		require($cakePath.'config'.DS.'paths.php');
	}

	function isHelpParam($param) {
		return ($param == 'help' || $param == '--help');
	}

	function prepareParams($originalParams, $elementsToRemove) {
		$params = $originalParams;

		for ($i = 0; $i < $elementsToRemove; $i++) {
			array_shift($params);
		}

		return $params;
	}

	function readConfigFile($fileName) {
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

	function setHttpHost() {
		$configFile = APP_PATH.'config'.DS.'test-config.php';
		$httpHost = 'localhost';
		
		if (file_exists($configFile)) {
			require($configFile);
			$config = new TestConfig();
			$httpHost = $config->httpHost;
		}
		
		$_SERVER['HTTP_HOST'] = $httpHost;
	}
	
	function showHelp() {
		echo "Usage: php test.php app-alias [param1, ...]\n";
	}
?>