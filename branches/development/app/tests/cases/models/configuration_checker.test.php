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
	
	public function testCheckForRequiredConfigKeys() {
		$this->checker->setRequiredConfigKeys(array());
		$this->assertIdentical(array(), $this->checker->publicCheckForRequiredConfigKeys());
		
		$configKey = new ConfigDefinition('Noserub.required_key');
		$this->checker->setRequiredConfigKeys(array($configKey));
		
		$result = $this->checker->publicCheckForRequiredConfigKeys();
		$this->assertTrue(isset($result[$configKey->getKey()]));
	}
	
	public function testValidationOfBooleanConfigValue() {
		$configKey = 'Noserub.key'; 
		$configDefinition = new ConfigDefinition($configKey, 'BooleanValidator');
		$this->checker->setRequiredConfigKeys(array($configDefinition));
		
		Configure::write($configKey, true);
		$this->assertIdentical(array(), $this->checker->publicCheckForRequiredConfigKeys());
		
		Configure::write($configKey, 'not_a_boolean_value');
		$result = $this->checker->publicCheckForRequiredConfigKeys();
		$this->assertTrue(isset($result[$configKey]));
	}
}

class MyConfigurationChecker extends ConfigurationChecker {
	public function setObsoleteConfigKeys($keys) {
		$this->obsoleteConfigKeys = $keys;
	}
	
	public function setObsoleteConstants($constants) {
		$this->obsoleteConstants = $constants;
	}

	public function setRequiredConfigKeys($keys) {
		$this->requiredConfigKeys = $keys;
	}
	
	public function publicCheckForObsoleteConfigKeys() {
		return $this->checkForObsoleteConfigKeys();
	}
	
	public function publicCheckForObsoleteConstants() {
		return $this->checkForObsoleteConstants();
	}
	
	public function publicCheckForRequiredConfigKeys() {
		return $this->checkForRequiredConfigKeys();
	}
}

class ConfigDefinitionTest extends CakeTestCase {
	public function testConfigDefinitionWithoutValidator() {
		$definition = new ConfigDefinition('key');
		$this->assertEqual('key', $definition->getKey());
		$this->assertFalse($definition->hasValidator());
		$this->assertNull($definition->getValidatorName());
	}
	
	public function testConfigDefinitionWithValidator() {
		$definition = new ConfigDefinition('key', 'validator');
		$this->assertEqual('key', $definition->getKey());
		$this->assertTrue($definition->hasValidator());
		$this->assertEqual('validator', $definition->getValidatorName());		
	}
}

class BooleanValidatorCheckerTest extends CakeTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new BooleanValidator();
	}
	
	public function testValidate() {
		$this->assertIdentical(true, $this->validator->validate(true));
		$this->assertIdentical(true, $this->validator->validate(false));
		$this->assertTrue(is_string($this->validator->validate(0)));
		$this->assertTrue(is_string($this->validator->validate(1)));
	}
}

class FullBaseUrlValidatorTest extends CakeTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new FullBaseUrlValidator();
	}
	
	public function testValidate() {
		$this->assertIdentical(true, $this->validator->validate('http://example.com/'));
		$this->assertTrue(is_string($this->validator->validate('http://example.com')));
	}
}

class RegistrationTypeValidatorTest extends CakeTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new RegistrationTypeValidator();
	}
	
	public function testValidate() {
		$this->assertIdentical(true, $this->validator->validate('all'));
		$this->assertIdentical(true, $this->validator->validate('none'));
		$this->assertIdentical(true, $this->validator->validate('invitation'));
		$this->assertTrue(is_string($this->validator->validate('some text')));
		$this->assertTrue(is_string($this->validator->validate('')));
	}
}