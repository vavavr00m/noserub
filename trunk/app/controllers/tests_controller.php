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
	public $uses = array();
	
	public function index() {
		$groupTestFileNames = Configure::listObjects('file', APP.'tests'.DS.'groups');
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
	
	public function all() {
		$this->executeTestTask();
	}
	
	public function component($componentName) {
		$this->executeTestTask($this->action, $componentName);
	}
	
	public function components() {
		$this->executeTestTask($this->action);
	}
	
	public function controller($controllerName) {
		$this->executeTestTask($this->action, $controllerName);
	}
	
	public function controllers() {
		$this->executeTestTask($this->action);
	}
	
	public function helper($helperName) {
		$this->executeTestTask($this->action, $helperName);
	}
	
	public function helpers() {
		$this->executeTestTask($this->action);
	}
	
	public function group($groupName) {
		$this->executeTestTask($this->action, $groupName);
	}
	
	public function model($modelName) {
		$this->executeTestTask($this->action, $modelName);
	}
	
	public function models() {
		$this->executeTestTask($this->action);
	}
	
	public function plugin($pluginName) {
		$this->executeTestTask($this->action, $pluginName);
	}
	
	public function plugins() {
		$this->executeTestTask($this->action);
	}
	
	private function executeTestTask($param1 = '', $param2 = '') {
		putenv('display=html');
		$out = shell_exec($this->getPHPCommand() . ' ' . VENDORS . 'testsuite' . DS . 'test.php '. $this->getAppAlias() . ' ' . $param1 . ' ' .$param2);
		echo $out;
		
		exit();
	}
	
	private function getAppAlias() {
		$appAlias = '';
		$apps = $this->readConfigFile(VENDORS.'testsuite'.DS.'apps.ini');
		$apps = array_flip($apps);
		if (array_key_exists(ROOT.DS.APP_DIR, $apps)) {
			$appAlias = $apps[ROOT.DS.APP_DIR];
		}
		
		return $appAlias;
	}
	
	private function getPHPCommand() {
		$phpCommand = 'php';
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$phpCommand .= '.exe';
		}
		
		return $phpCommand;
	}
	
	private function readConfigFile($fileName) {
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