<?php
/**
 * Base class for test cases.
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

	$dispatcher = file_get_contents(CAKE.'dispatcher.php');
	$dispatcher = str_replace('<?php', '', $dispatcher);
	$dispatcher = str_replace('?>', '', $dispatcher);
	$dispatcher = str_replace("uses('router', DS.'controller'.DS.'controller');", "uses('router');", $dispatcher);
	
	eval($dispatcher);
	
	uses('security', 'session', 'sanitize', 'string');
	vendor('simpletest'.DS.'mock_objects');
	vendor('testsuite'.DS.'test_dispatcher');
	vendor('testsuite'.DS.'session');
	vendor('testsuite'.DS.'error');
	vendor('testsuite'.DS.'cookie');
	vendor('testsuite'.DS.'session_helper');
	
	class CakeTestCase extends UnitTestCase {
		var $fixtures = array();
		var $controller = null;
		var $error = null;
		var $response = array();
		var $flash = array();
		var $session = array();
		var $cookie = array();
		
		function run(&$reporter) {
            $context = &SimpleTest::getContext();
			$context->setTest($this);
			$context->setReporter($reporter);
            $this->_reporter = &$reporter;
            $reporter->paintCaseStart($this->getLabel());
			$this->skip();
            if (! $this->_should_skip) {
            	$this->oneTimeSetUp();
                foreach ($this->getTests() as $method) {
                    if ($reporter->shouldInvoke($this->getLabel(), $method)) {
                        $invoker = &$this->_reporter->createInvoker($this->createInvoker());
                        $invoker->before($method);
                        $this->loadFixtures();
                        $invoker->invoke($method);
                        $invoker->after($method);
                    }
                }
                $this->oneTimeTearDown();
            }
            $reporter->paintCaseEnd($this->getLabel());
            unset($this->_reporter);
            return $reporter->getStatus();
        }
        
        
		
		/**
		 * Override this function in subclasses. It is executed once per test case, before any tests are executed.
		 */
		function oneTimeSetUp() {
			// empty
		}
		
		/**
		 * Override this function in subclasses. It is executed once per test case, after all tests have been executed. 
		 */
		function oneTimeTearDown() {
			// empty
		}
		
        /**
         * Simulates a get request.
         * 
         * @param string $url The url to request.
         * @param array $sessionData An array of session variables.
         * @param array $cookieData An array of cookie data.
         */
        function get($url, $sessionData = array(), $cookieData = array()) {
        	unset($_POST['data']);
        	$this->doRequest($url, $sessionData, $cookieData);
        }
        
        /**
         * Simulates a post request.
         * 
         * @param string $url The url to request.
         * @param array $data  The post data.
         * @param array $sessionData An array of session variables.
         * @param array $cookieData An array of cookie data.
         */
        function post($url, $data, $sessionData = array(), $cookieData = array()) {
        	$_POST['data'] = $data;
        	$this->doRequest($url, $sessionData, $cookieData);
        }
        
        
        
        /**
         * Asserts that the default flash message was set.
         * @param string $flashMessage The expected flash message
         * @param string $message The message to display
         * @return true if test passes, false otherwise
         */
        function assertFlash($flashMessage, $message = false) {
        	if (!$message) {
        		if (!empty($this->flash)) {
        			$message = 'Expected ' . $flashMessage . ', but flash message was ' . $this->flash['flash'];
        		} else {
        			$message = 'Expected ' . $flashMessage . ', but there was no flash message set';
        		}
        	}
        	
        	if (isset($this->flash['flash']) && $this->flash['flash'] === $flashMessage) {
        		return $this->pass($message);
        	} else {
        		return $this->fail($message);
        	}
        }
        
        /**
         * Asserts that a previous request did a redirect.
         * @param string $url The expected redirect location.
         * @param string $message The message to display.
         * @return true if test passes, false otherwise
         */
        function assertRedirectedTo($url, $message = false) {
        	if (isset($this->controller)) {
	        	if (!$message) {
	        		if ($this->controller->redirectUrl != null) {
	        			$message = 'Expected redirect to '.$url.', but redirect was to '. $this->controller->redirectUrl;
	        		} else {
	        			$message = 'Expected redirect to '.$url.', but there was no redirect';
	        		}
	        	}
	        	
	        	if ($this->controller->redirectUrl == $url) {
	        		return $this->pass($message);
	        	} else {
	        		return $this->fail($message);
	        	}
        	} else {
        		return $this->fail('Expected redirect to '.$url.', but an error occured');
        	}
        }
        
        /**
         * Asserts that a previous request has returned the expected response.
         * @param int $responseType The expected response type
         * @param string $message Message to display.
         * @return true if test passes, false otherwise.
         */
        function assertResponse($responseType, $message = false) {
        	if (!$message) {
        		$responseTypes = array(SUCCESS => 'Success',
        							   REDIRECT => 'Redirect',
        							   ERROR => 'Error',
        							   ERROR_404 => 'Error 404',
        							   MISSING_CONTROLLER => 'Missing controller',
        							   MISSING_ACTION => 'Missing action',
        							   PRIVATE_ACTION => 'Private action',
        							   MISSING_TABLE => 'Missing table',
        							   MISSING_DATABASE => 'Missing database',
        							   MISSING_VIEW => 'Missing view',
        							   MISSING_LAYOUT => 'Missing layout',
        							   MISSING_CONNECTION => 'Missing connection',
        							   MISSING_HELPER_FILE => 'Missing helper file',
        							   MISSING_HELPER_CLASS => 'Missing helper class',
        							   MISSING_COMPONENT_FILE => 'Missing component file',
        							   MISSING_COMPONENT_CLASS => 'Missing component class',
        							   MISSING_MODEL => 'Missing model');

				$message = 'Expected response type <' . $responseTypes[$responseType] . '>, ';

        		if (!empty($this->response)) {
        			$message .= 'but response type was <' . $responseTypes[$this->response['type']] . '>';
        		} else {
        			$message .= 'but there was no request made';
        		}
        	}
        	
        	if (isset($this->response['type']) && $this->response['type'] === $responseType) {
        		return $this->pass($message);
        	} else {
        		return $this->fail($message);
        	}
        }
        
		private function checkForColumnAvailability($db, $tableName) {
			$result = $db->query('DESC '.$tableName);
			$columns = array('modified' => false, 'created' => false, 'updated' => false);
   			
   			foreach ($result as $column) {
   				if (array_key_exists($column['COLUMNS']['Field'], $columns)) {
   					$columns[$column['COLUMNS']['Field']] = true;
   				}
   			}
   			
   			return $columns;
		}
        
		private function doRequest($url, $sessionData, $cookieData) {
        	$this->response = array();
        	$errorHandler =& ErrorHandlerHelper::getInstance();
        	$errorHandler->error = null;
        	
			$sessionComponent =& SessionComponent::getInstance();
			$sessionComponent->destroy();
			if ($sessionData != null) {
				foreach ($sessionData as $key => $data) {
        			$sessionComponent->write($key, $data);
        		}
			}
        	
        	$cookieComponent =& CookieComponent::getInstance();
        	$cookieComponent->destroy();
        	foreach ($cookieData as $key => $value) {
        		$cookieComponent->write($key, $value);
        	}
        	
        	Router::reload();
        	$dispatcher = new TestDispatcher();
        	$dispatcher->dispatch($url);

			$this->controller =& $dispatcher->controller;
			$this->session = $sessionComponent->session;
			$this->flash = $sessionComponent->flash;
			$this->error = $errorHandler->error;
			$this->cookie = $cookieComponent->cookieData;

        	if ($this->error == null) {
        		if ($this->controller->redirectUrl != null) {
        			$this->response['type'] = REDIRECT;
        		} else {
        			$this->response['type'] = SUCCESS;
        		}
        	} else {
        		$this->response['type'] = $this->error;
        	}
        	
        	$this->response['redirectUrl'] = $this->controller->redirectUrl;
        	$this->response['flashMessage'] = $this->controller->flashMessage;
        }
        
		private function getAdditionalColumns($fixtureColumns, $columns) {
			$additionalColumns = array();
        	$possibleColumns = array('created', 'modified', 'updated');

        	foreach ($possibleColumns as $column) {
        		if (!in_array($column, $fixtureColumns) && $columns[$column] === true) {
        			$additionalColumns[] = $column;
        		}
        	}
        	
        	return $additionalColumns;
		}
        
		private function handleValue($value, $db) {
			$result = '';
			
			if (is_string($value)) {
				$result = $db->value($value);
			} elseif (is_bool($value)) {
				$result = $value == true ? 'true' : 'false';
			} else {
				$result = $value;
			}
			
			return $result;
		}
		
		private function loadFixtures() {
			if (!empty($this->fixtures)) {
        		$db =& ConnectionManager::getDataSource('default');

				$fixturePath = APP.'tests'.DS.'fixtures';

				if (strpos($this->_reporter->_test_stack[1], APP.'plugins') !== false) {
					$testFile = $this->_reporter->_test_stack[1];
					$testFileWithoutBasePath = substr($testFile, strlen(APP.'plugins'.DS));
					$pluginName = substr($testFileWithoutBasePath, 0, strpos($testFileWithoutBasePath, DS));
					$fixturePath = APP.'plugins'.DS.$pluginName.DS.'tests'.DS.'fixtures';
				}
        		
        		foreach ($this->fixtures as $fixture) {
        			require_once($fixturePath.DS.Inflector::underscore($fixture).'.php');
        			
        			$f = new $fixture();
        			
        			$variables = get_class_vars($fixture);
		
        			$db->execute('DELETE FROM '.Inflector::underscore($fixture));
        			$columns = $this->checkForColumnAvailability($db, Inflector::underscore($fixture));
        			
        			foreach ($variables as $name => $data) {
        				if ($name != 'columns') {
        					$this->$name = array_combine($f->columns, $f->$name);
        					$values = array();
	
        					foreach ($f->$name as $value) {
        						$values[] = $this->handleValue($value, $db);
        					}
        					
        					$additionalColumns = $this->getAdditionalColumns($f->columns, $columns);
        					$noOfAdditionalColumns = count($additionalColumns);
        					
        					for ($i = 0; $i < $noOfAdditionalColumns; $i++) {
        						$values[] = 'NOW()';
        					}
        					
        					$sql = 'INSERT INTO '.Inflector::underscore($fixture).' ('.implode(',', am($f->columns, $additionalColumns)).') VALUES ('.implode(',', $values).')';
        					$db->execute($sql);
        				}
        			}
        		}
        	}
		}
	}
?>