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

	uses('object', 'controller'.DS.'controller', 'configure', 'inflector');

	if (file_exists(CONFIGS.'test-database.php')) {
		$connectionManager = file_get_contents(LIBS.'model'.DS.'connection_manager.php');
		$connectionManager = str_replace('<?php', '', $connectionManager);
		$connectionManager = str_replace('?>', '', $connectionManager);
		$connectionManager = str_replace("config('database');", "config('test-database');", $connectionManager);
		
		eval($connectionManager);
	} else {
		echo "File config/test-database.php not found. Copy config/database.php and configure the test database(s).\n";
		exit;
	}

	$model = file_get_contents(LIBS.'model'.DS.'model.php');
	$model = str_replace('<?php', '', $model);
	$model = str_replace('?>', '', $model);
	$model = str_replace("'model' . DS . 'connection_manager',", "", $model);

	eval($model);
	
	vendor('simpletest'.DS.'mock_objects');
	vendor('simpletest'.DS.'test_case');
	vendor('simpletest'.DS.'unit_tester');
	vendor('simpletest'.DS.'reporter');
	vendor('testsuite'.DS.'cake_test_case');
	vendor('testsuite'.DS.'cake_group_test');

	class TestTask {
		var $test = null;
		
		function execute($params) {
			$this->test = &new CakeGroupTest();
			
			if (empty($params)) {
				$this->__injectTestableController();
				$this->__addAllTests();
			} else {
				$paramCount = count($params);
				switch ($params[0]) {
					case 'models':
						$this->__addModelTests();
						break;
					case 'helpers':
						$this->__addHelperTests();
						break;
					case 'components':
						$this->__addComponentTests();
						break;
					case 'controllers':
						$this->__injectTestableController();
						$this->__addControllerTests();
						break;
					case 'plugins':
						$this->__injectTestableController();
						$this->__addPluginTests();
						break;
					case 'model':
						$this->__loadModels();
						if ($paramCount == 2) {
							$this->__addTest($params[1], 'models');
						}
						break;
					case 'helper':
						if ($paramCount == 2) {
							loadHelper($params[1]);
							$this->__addTest($params[1], 'helpers');
						}
						break;
					case 'component':
						if ($paramCount == 2) {
							loadComponent($params[1]);
							$this->__addTest($params[1], 'components');
						}
						break;
					case 'controller':
						$this->__injectTestableController();
						$this->__loadControllersAndModels();
						if ($paramCount == 2) {
							$this->__addTest($params[1], 'controllers');
						}
						break;
					case 'plugin':
						$this->__injectTestableController();
						if ($paramCount == 2) {
							$this->__addPluginTests($params[1]);
						}
						breaK;
					case 'group':
						$this->__injectTestableController();
						$this->__loadControllersAndModels();
						if ($paramCount == 2) {
							$this->__addTest($params[1], 'groups', '.php');
						}
						break;
					default:
						break;
				}
			}
			
			$this->test->run($this->__createReporter());
		}
		
	function __createReporter() {
			$reporter = null;
			
			if (getEnv('display') == 'html') {
				$reporter = new HtmlReporter();
			} else {
				$reporter = new TextReporter();
			}
			
			return $reporter;
		}
		
		function __addAllTests() {
			$this->__loadControllersAndModels();

			$this->__addTests('models');			
			$this->__addTests('controllers');
			$this->__addHelperTests();
			$this->__addComponentTests();
			$this->__addPluginTests();
			$this->test->_label = 'All tests';
		}
		
		function __addHelperTests() {
			$this->test->_label = 'Helper tests';
			
			$tests = listClasses(APP.'tests'.DS.'helpers');
			
			foreach ($tests as $test) {
				if (substr_count($test, '_test.php') == 1) {
					loadHelper(substr($test, 0, strpos($test, '_test.php')));
					$this->test->addTestFile('helpers'.DS.$test);
				}
			}
		}
		
		function __addModelTests() {
			$this->__loadModels();
			$this->test->_label = 'Model tests';
			$this->__addTests('models');
		}
		
		function __addComponentTests() {
			$this->test->_label = 'Component tests';
			
			$tests = listClasses(APP.'tests'.DS.'components');
			
			foreach ($tests as $test) {
				if (substr_count($test, '_test.php') == 1) {
					loadComponent(substr($test, 0, strpos($test, '_test.php')));
					$this->test->addTestFile('components'.DS.$test);
				}
			}
		}
		
		function __addControllerTests() {
			ob_start();
			$this->__loadControllersAndModels();
			
			$this->test->_label = 'Controller tests';
			$this->__addTests('controllers');
		}
		
		function __addPluginTests($pluginName = null) {
			if ($pluginName == null) {
				$this->test->_label = 'Plugin Tests';
				uses('Folder');
				$pluginsFolder = new Folder(APP.'plugins');
				$folderContent = $pluginsFolder->ls(true, true);
				
				foreach($folderContent[0] as $pluginName) {
					$this->__addTestsForSinglePlugin($pluginName);
				}
			} else {
				$this->test->_label = 'Tests for plugin '.$pluginName;
				$this->__addTestsForSinglePlugin($pluginName);
			}
		}
		
		function __addTestsForSinglePlugin($pluginName) {
			if (file_exists(APP.'plugins'.DS.$pluginName.DS.'tests')) {
				$this->__addPluginModelTests($pluginName);
				$this->__addPluginControllerTests($pluginName);
				$this->__addPluginHelperTests($pluginName);
				$this->__addPluginComponentTests($pluginName);
			}
		}
		
		function __addPluginModelTests($pluginName) {
			uses('Folder');
			loadPluginModels($pluginName);
			
			$modelTestFolder = new Folder(APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'models');
			$testFiles = $modelTestFolder->findRecursive('.*.php');
					
			foreach ($testFiles as $testFile) {
				$this->test->addTestFile($testFile, false);
			}
		}
		
		function __addPluginControllerTests($pluginName) {
			uses('Folder');
			
			$controllerTestFolder = new Folder(APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'controllers');
			$testFiles = $controllerTestFolder->findRecursive('.*.php');
			
			foreach ($testFiles as $testFile) {
				$this->test->addTestFile($testFile, false);
			}
		}
		
		function __addPluginComponentTests($pluginName) {
			uses('Folder');
			
			$componentTestFolder = new Folder(APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'components');
			$testFiles = $componentTestFolder->findRecursive('.*.php');
			
			foreach ($testFiles as $testFile) {
				$this->test->addTestFile($testFile, false);
			}
		}
		
		function __addPluginHelperTests($pluginName) {
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
		
		function __addTest($objectName, $folderName, $postFix = '_test.php') {
			$this->test->_label = 'Test of ' . Inflector::singularize($folderName) . ' '. $objectName;
			$fileName = Inflector::underscore($objectName);
			$this->test->addTestFile($folderName.DS.$fileName.$postFix);
		}
		
		function __addTests($folderWithTests) {
			$tests = listClasses(APP.'tests'.DS.$folderWithTests);
			
			foreach ($tests as $test) {
				if (substr_count($test, '_test.php') == 1) {
					$this->test->addTestFile($folderWithTests.DS.$test);
				}
			}
		}
		
		function __injectTestableController() {
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

		function __loadControllersAndModels() {
			$this->__loadModels();
			$this->__loadControllers();
		}
		
		function __loadControllers() {
			// FIXME calling this function breaks the test suite!!!
			//loadControllers();
		}
		
		function __loadModels() {
			$appModel = 'app_model.php';
			
			if (file_exists(APP . $appModel)) {
				require(APP . $appModel);
			} else {
				require(CAKE . $appModel);
			}
			
			loadModels();
		}
	}
?>