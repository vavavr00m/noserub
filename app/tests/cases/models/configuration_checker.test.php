<?php

App::import('Model', 'ConfigurationChecker');

class ConfigurationCheckerTest extends CakeTestCase {
	private $checker = null;
	
	public function setUp() {
		$this->checker = new MyConfigurationChecker();
	}
	
	public function testCheck() {
		$this->assertNoValidationErrors($this->checker->check());
	}
	
	public function testCheckForObsoleteConfigKeys() {
		$this->checker->setObsoleteConfigKeys(array());
		$this->assertNoValidationErrors($this->checker->publicCheckForObsoleteConfigKeys());
		
		$configKey = 'obsolete_config_key';
		Configure::write($configKey, '');
		$this->checker->setObsoleteConfigKeys(array($configKey));

		$result = $this->checker->publicCheckForObsoleteConfigKeys();
		$this->assertValidationError($configKey, $result);
	}
	
	public function testCheckForObsoleteConstants() {
		$this->checker->setObsoleteConstants(array());
		$this->assertNoValidationErrors($this->checker->publicCheckForObsoleteConstants());
		
		$constantName = 'OBSOLETE_CONSTANT';
		define($constantName, '');
		$this->checker->setObsoleteConstants(array($constantName));
		
		$result = $this->checker->publicCheckForObsoleteConstants();
		$this->assertValidationError($constantName, $result);
	}
	
	public function testCheckForRequiredConfigSettings() {
		$this->checker->setConfigDefinitions(array());
		$this->assertNoValidationErrors($this->checker->publicCheckForRequiredConfigSettings());
		
		$configKey = 'NoseRub.required_key';
		$this->checker->setConfigDefinitions(array(new ConfigDefinition($configKey)));
		
		$result = $this->checker->publicCheckForRequiredConfigSettings();
		$this->assertValidationError($configKey, $result);
	}
	
	public function testValidationOfConfigValue() {
		$configKey = 'NoseRub.key'; 
		$configDefinition = new ConfigDefinition($configKey, new MyConfigValueValidator());
		$this->checker->setConfigDefinitions(array($configDefinition));
		
		Configure::write($configKey, 'valid_value');
		$this->assertNoValidationErrors($this->checker->publicCheckForRequiredConfigSettings());
		
		Configure::write($configKey, 'invalid_value');
		$result = $this->checker->publicCheckForRequiredConfigSettings();
		$this->assertValidationError($configKey, $result);
	}
	
	private function assertNoValidationErrors($validationResult) {
		$this->assertIdentical(array(), $validationResult);
	}
	
	private function assertValidationError($key, $validationResult) {
		$this->assertTrue(isset($validationResult[$key]));
	}
}

class MyConfigurationChecker extends ConfigurationChecker {
	public function setObsoleteConfigKeys($keys) {
		$this->obsoleteConfigKeys = $keys;
	}
	
	public function setObsoleteConstants($constants) {
		$this->obsoleteConstants = $constants;
	}

	public function setConfigDefinitions($keys) {
		$this->configDefinitions = $keys;
	}
	
	public function publicCheckForObsoleteConfigKeys() {
		return $this->checkForObsoleteConfigKeys();
	}
	
	public function publicCheckForObsoleteConstants() {
		return $this->checkForObsoleteConstants();
	}
	
	public function publicCheckForRequiredConfigSettings() {
		return $this->checkForRequiredConfigSettings();
	}
}

class MyConfigValueValidator implements ConfigValueValidator {
	public function validate($value) {
		if ($value == 'valid_value') {
			return true;
		}
		
		return 'invalid value';
	}
}

class ConfigDefinitionTest extends CakeTestCase {
	public function testConfigDefinitionWithoutValidator() {
		$definition = new ConfigDefinition('key');
		$this->assertEqual('key', $definition->getKey());
		$this->assertFalse($definition->hasValidator());
		$this->assertNull($definition->getValidator());
	}
	
	public function testConfigDefinitionWithValidator() {
		$validator = new MyConfigValueValidator();
		$definition = new ConfigDefinition('key', $validator);
		$this->assertEqual('key', $definition->getKey());
		$this->assertTrue($definition->hasValidator());
		$this->assertEqual($validator, $definition->getValidator());
	}
}

class ValidatorTestCase extends CakeTestCase {
	public function assertNoValidationError($validationResult) {
		$this->assertIdentical(true, $validationResult);
	}
	
	public function assertValidationError($validationResult) {
		$this->assertTrue(is_string($validationResult));
	}
}

class BooleanValidatorCheckerTest extends ValidatorTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new BooleanValidator();
	}
	
	public function testValidate() {
		$this->assertNoValidationError($this->validator->validate(true));
		$this->assertNoValidationError($this->validator->validate(false));
		$this->assertValidationError($this->validator->validate(0));
		$this->assertValidationError($this->validator->validate(1));
	}
}

class FalseOrNonEmptyStringValidatorTest extends ValidatorTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new FalseOrNonEmptyStringValidator();
	}
	
	public function testValidate() {
		$this->assertNoValidationError($this->validator->validate(false));
		$this->assertNoValidationError($this->validator->validate('valid_string'));
		$this->assertValidationError($this->validator->validate(true));
		$this->assertValidationError($this->validator->validate(''));
	}
}

class FullBaseUrlValidatorTest extends ValidatorTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new FullBaseUrlValidator();
	}
	
	public function testValidate() {
		$this->assertNoValidationError($this->validator->validate('http://example.com/'));
		$this->assertValidationError($this->validator->validate('http://example.com'));
	}
}

class NumericValidatorTest extends ValidatorTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new NumericValidator();
	}
	
	public function testValidate() {
		$this->assertNoValidationError($this->validator->validate(123));
		$this->assertValidationError($this->validator->validate(''));
		$this->assertValidationError($this->validator->validate(false));
	}
}

class RegistrationTypeValidatorTest extends ValidatorTestCase {
	private $validator = null;
	
	public function setUp() {
		$this->validator = new RegistrationTypeValidator();
	}
	
	public function testValidate() {
		$this->assertNoValidationError($this->validator->validate('all'));
		$this->assertNoValidationError($this->validator->validate('none'));
		$this->assertNoValidationError($this->validator->validate('invitation'));
		$this->assertValidationError($this->validator->validate('some text'));
		$this->assertValidationError($this->validator->validate(''));
	}
}