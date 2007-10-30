<?php
/**
 * Test suite controller which allows to run the tests via web UI. Does not work in safe mode!
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

	uses('Folder');

	class TestsController extends AppController {
		var $uses = array();
		
		function index() {
			$groupTestFileNames = listClasses(APP.'tests'.DS.'groups');
			$groupTestNames = array();
			$pluginsWithTests = array();
			
			foreach ($groupTestFileNames as $groupTestFileName) {
				$groupTestNames[] = Inflector::camelize(str_replace('.php', '', $groupTestFileName));
			}
			
			$this->set('groupTestNames', $groupTestNames);

			$pluginsFolder = new Folder(APP.'plugins');
			$folderContent = $pluginsFolder->ls(true, true);
			
			foreach($folderContent[0] as $pluginName) {
				if (file_exists(APP.'plugins'.DS.$pluginName.DS.'tests')) {
					$pluginsWithTests[] = $pluginName;
				}
			}
			
			$this->set('pluginsWithTests', $pluginsWithTests);
		}
		
		function all() {
			$this->__executeTestTask();
		}
		
		function component($componentName) {
			$this->__executeTestTask($this->action, $componentName);
		}
		
		function components() {
			$this->__executeTestTask($this->action);
		}
		
		function controller($controllerName) {
			$this->__executeTestTask($this->action, $controllerName);
		}
		
		function controllers() {
			$this->__executeTestTask($this->action);
		}
		
		function helper($helperName) {
			$this->__executeTestTask($this->action, $helperName);
		}
		
		function helpers() {
			$this->__executeTestTask($this->action);
		}
		
		function group($groupName) {
			$this->__executeTestTask($this->action, $groupName);
		}
		
		function model($modelName) {
			$this->__executeTestTask($this->action, $modelName);
		}
		
		function models() {
			$this->__executeTestTask($this->action);
		}
		
		function plugin($pluginName) {
			$this->__executeTestTask($this->action, $pluginName);
		}
		
		function plugins() {
			$this->__executeTestTask($this->action);
		}
		
		function __executeTestTask($param1 = '', $param2 = '') {
			putenv('display=html');
			$out = shell_exec($this->__getPHPCommand() . ' ' . VENDORS . 'testsuite' . DS . 'test.php '. $this->__getAppAlias() . ' ' . $param1 . ' ' .$param2);
			echo $out;
			
			exit();
		}
		
		function __getAppAlias() {
			$appAlias = '';
			$apps = $this->__readConfigFile(VENDORS.'testsuite'.DS.'apps.ini');
			$apps = array_flip($apps);
			if (array_key_exists(ROOT.DS.APP_DIR, $apps)) {
				$appAlias = $apps[ROOT.DS.APP_DIR];
			}
			
			return $appAlias;
		}
		
		function __getPHPCommand() {
			$phpCommand = 'php';
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				$phpCommand .= '.exe';
			}
			
			return $phpCommand;
		}
		
		function __readConfigFile($fileName) {
			$fileLineArray = file($fileName);
			$iniSetting = array();
			
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
	}
?>