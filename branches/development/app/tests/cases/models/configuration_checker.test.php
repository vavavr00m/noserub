<?php

App::import('Model', 'ConfigurationChecker');

class ConfigurationCheckerTest extends CakeTestCase {
	private $checker = null;
	
	public function setUp() {
		$this->checker = new MyConfigurationChecker();
	}
	
	public function testCheck() {
		$this->assertIdentical(array(), $this->checker->check());
	}
	
	public function testCheckForObsoleteConfigKeys() {
		$this->checker->setObsoleteConfigKeys(array());
		$this->assertIdentical(array(), $this->checker->publicCheckForObsoleteConfigKeys());
		
		$configKey = 'obsolete_config_key';
		Configure::write($configKey, '');
		$this->checker->setObsoleteConfigKeys(array($configKey));

		$result = $this->checker->publicCheckForObsoleteConfigKeys();
		$this->assertTrue(isset($result[$configKey]));
	}
	
	public function testCheckForObsoleteConstants() {
		$this->checker->setObsoleteConstants(array());
		$this->assertIdentical(array(), $this->checker->publicCheckForObsoleteConstants());
		
		$constantName = 'OBSOLETE_CONSTANT';
		define($constantName, '');
		$this->checker->setObsoleteConstants(array($constantName));
		
		$result = $this->checker->publicCheckForObsoleteConstants();
		$this->assertTrue(isset($result[$constantName]));
	}
}

class MyConfigurationChecker extends ConfigurationChecker {
	public function setObsoleteConfigKeys($keys) {
		$this->obsoleteConfigKeys = $keys;
	}
	
	public function setObsoleteConstants($constants) {
		$this->obsoleteConstants = $constants;
	}
	
	public function publicCheckForObsoleteConfigKeys() {
		return $this->checkForObsoleteConfigKeys();
	}
	
	public function publicCheckForObsoleteConstants() {
		return $this->checkForObsoleteConstants();
	}
}