<?php
/**
 * Task which executes the tests..
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

	uses('controller'.DS.'controller');

	if (file_exists(CONFIGS.'test-database.php')) {
// FIXME this doesn't work any longer... some workaround is needed
/*		$connectionManager = file_get_contents(LIBS.'model'.DS.'connection_manager.php');
		$connectionManager = str_replace('<?php', '', $connectionManager);
		$connectionManager = str_replace('?>', '', $connectionManager);
		$connectionManager = str_replace("config('database');", "config('test-database');", $connectionManager);
		
		eval($connectionManager);*/
	} else {
		echo "File config/test-database.php not found. Copy config/database.php and configure the test database(s).\n";
		exit;
	}

	$model = file_get_contents(LIBS.'model'.DS.'model.php');
	$model = str_replace('<?php', '', $model);
	$model = str_replace('?>', '', $model);
	$model = str_replace("'model' . DS . 'connection_manager',", "", $model);

	eval($model);
	
	vendor('simpletest'.DS.'test_case');
	vendor('simpletest'.DS.'unit_tester');
	vendor('simpletest'.DS.'reporter');
	vendor('testsuite'.DS.'cake_test_case');
	vendor('testsuite'.DS.'cake_group_test');

	class TestTask {
		var $test = null;
		
		function execute($params) {
			$this->test = new CakeGroupTest();
			
			if (empty($params)) {
				$this->injectTestableController();
				$this->addAllTests();
			} else {
				$paramCount = count($params);
				switch ($params[0]) {
					case 'models':
						$this->addModelTests();
						break;
					case 'helpers':
						$this->addHelperTests();
						break;
					case 'components':
						$this->addComponentTests();
						break;
					case 'controllers':
						$this->injectTestableController();
						$this->addControllerTests();
						break;
					case 'plugins':
						$this->injectTestableController();
						$this->addPluginTests();
						break;
					case 'model':
						$this->loadModels();
						if ($paramCount == 2) {
							$this->addTest($params[1], 'models');
						}
						break;
					case 'helper':
						if ($paramCount == 2) {
							loadHelper($params[1]);
							$this->addTest($params[1], 'helpers');
						}
						break;
					case 'component':
						if ($paramCount == 2) {
							loadComponent($params[1]);
							$this->addTest($params[1], 'components');
						}
						break;
					case 'controller':
						$this->injectTestableController();
						$this->loadControllersAndModels();
						if ($paramCount == 2) {
							$this->addTest($params[1], 'controllers');
						}
						break;
					case 'plugin':
						$this->injectTestableController();
						if ($paramCount == 2) {
							$this->addPluginTests($params[1]);
						}
						breaK;
					case 'group':
						$this->injectTestableController();
						$this->loadControllersAndModels();
						if ($paramCount == 2) {
							$this->addTest($params[1], 'groups', '.php');
						}
						break;
					default:
						break;
				}
			}
			
			$this->test->run($this->createReporter());
		}
		
		private function createReporter() {
			$reporter = null;
			
			if (getEnv('display') == 'html') {
				$reporter = new HtmlReporter();
			} else {
				$reporter = new TextReporter();
			}
			
			return $reporter;
		}
		
		private function addAllTests() {
			$this->loadControllersAndModels();
			$this->addTests('models');			
			$this->addTests('controllers');
			$this->addHelperTests();
			$this->addComponentTests();
			$this->addPluginTests();
			$this->test->_label = 'All tests';
		}
		
		private function addHelperTests() {
			$this->test->_label = 'Helper tests';

			$tests = Configure::listObjects('file', APP.'tests'.DS.'helpers');
			
			foreach ($tests as $test) {
				if (substr_count($test, '_test.php') == 1) {
					App::import('Helper', substr($test, 0, strpos($test, '_test.php')));
					$this->test->addTestFile('helpers'.DS.$test);
				}
			}
		}
		
		private function addModelTests() {
			$this->loadModels();
			$this->test->_label = 'Model tests';
			$this->addTests('models');
		}
		
		private function addComponentTests() {
			$this->test->_label = 'Component tests';
			
			$tests = Configure::listObjects('file', APP.'tests'.DS.'components');
			
			foreach ($tests as $test) {
				if (substr_count($test, '_test.php') == 1) {
					App::import('Component', substr($test, 0, strpos($test, '_test.php')));
					$this->test->addTestFile('components'.DS.$test);
				}
			}
		}
		
		private function addControllerTests() {
			ob_start();
			$this->loadControllersAndModels();
			
			$this->test->_label = 'Controller tests';
			$this->addTests('controllers');
		}
		
		private function addPluginTests($pluginName = null) {
			if ($pluginName == null) {
				$this->test->_label = 'Plugin Tests';
				uses('Folder');
				$pluginsFolder = new Folder(APP.'plugins');
				$folderContent = $pluginsFolder->ls(true, true);
				
				foreach($folderContent[0] as $pluginName) {
					$this->addTestsForSinglePlugin($pluginName);
				}
			} else {
				$this->test->_label = 'Tests for plugin '.$pluginName;
				$this->addTestsForSinglePlugin($pluginName);
			}
		}
		
		private function addTestsForSinglePlugin($pluginName) {
			if (file_exists(APP.'plugins'.DS.$pluginName.DS.'tests')) {
				$this->addPluginModelTests($pluginName);
				$this->addPluginControllerTests($pluginName);
				$this->addPluginHelperTests($pluginName);
				$this->addPluginComponentTests($pluginName);
			}
		}
		
		private function addPluginModelTests($pluginName) {
			uses('Folder');
			loadPluginModels($pluginName);
			
			$modelTestFolder = new Folder(APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'models');
			$testFiles = $modelTestFolder->findRecursive('.*.php');
					
			foreach ($testFiles as $testFile) {
				$this->test->addTestFile($testFile, false);
			}
		}
		
		private function addPluginControllerTests($pluginName) {
			uses('Folder');
			
			$controllerTestFolder = new Folder(APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'controllers');
			$testFiles = $controllerTestFolder->findRecursive('.*.php');
			
			foreach ($testFiles as $testFile) {
				$this->test->addTestFile($testFile, false);
			}
		}
		
		private function addPluginComponentTests($pluginName) {
			uses('Folder');
			
			$componentTestFolder = new Folder(APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'components');
			$testFiles = $componentTestFolder->findRecursive('.*.php');
			
			foreach ($testFiles as $testFile) {
				$this->test->addTestFile($testFile, false);
			}
		}
		
		private function addPluginHelperTests($pluginName) {
			uses('Folder');
			
			$helperTestFolder = new Folder(APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'helpers');
			$testFiles = $helperTestFolder->findRecursive('.*.php');
			
			foreach ($testFiles as $testFile) {
				$pathWithoutExtension = substr($testFile, 0, strpos($testFile, '_test.php'));
				$fileWithoutPath = substr($pathWithoutExtension, strrpos($pathWithoutExtension, DS) + 1);
				loadPluginHelper($pluginName, $fileWithoutPath);
				$this->test->addTestFile($testFile, false);
			}
		}
		
		private function addTest($objectName, $folderName, $postFix = '_test.php') {
			$this->test->_label = 'Test of ' . Inflector::singularize($folderName) . ' '. $objectName;
			$fileName = Inflector::underscore($objectName);
			$this->test->addTestFile($folderName.DS.$fileName.$postFix);
		}
		
		private function addTests($folderWithTests) {
			$tests = Configure::listObjects('file', APP.'tests'.DS.$folderWithTests);
			
			foreach ($tests as $test) {
				if (substr_count($test, '_test.php') == 1) {
					$this->test->addTestFile($folderWithTests.DS.$test);
				}
			}
		}
		
		private function injectTestableController() {
			require(VENDORS.'testsuite'.DS.'testable_controller.php');
			$appController = 'app_controller.php';
			$appControllerContent = null;
			
			if (file_exists(APP . $appController)) {
				$appControllerContent = file_get_contents(APP . $appController);
			} else {
				$appControllerContent = file_get_contents(CAKE . $appController);
			}
			
			$appControllerContent = str_replace('<?php', '', $appControllerContent);
			$appControllerContent = str_replace('?>', '', $appControllerContent);
			$appControllerContent = str_replace('extends Controller', 'extends TestableController', $appControllerContent);

			eval($appControllerContent);
		}

		private function loadControllersAndModels() {
			$this->loadModels();
			$this->loadControllers();
		}
		
		private function loadControllers() {
			// FIXME if enabled, this causes a "cannot redeclare class" error
			//App::import('Controller', Configure::listObjects('controller'));
		}
		
		private function loadModels() {
			App::import('Model', Configure::listObjects('model', MODELS));
		}
	}
?>