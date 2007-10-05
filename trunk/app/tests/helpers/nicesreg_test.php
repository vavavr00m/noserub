<?php

	class NiceSRegTest extends CakeTestCase {
		var $helper = null;
		
		function setUp() {
			$this->helper = new NiceSRegHelper();
		}
		
		function testKey() {
			$result = $this->helper->key('email');
			$this->assertEqual('Email', $result);
			
			$result = $this->helper->key('dob');
			$this->assertEqual('Date of birth', $result);
		}
		
		function testValue() {
			$result = $this->helper->value('email', 'test@example.com');
			$this->assertEqual('test@example.com', $result);
			
			$result = $this->helper->value('fullname', 'Hans Muster');
			$this->assertEqual('Hans Muster', $result);
			
			$result = $this->helper->value('gender', 'M');
			$this->assertEqual('male', $result);
			
			$result = $this->helper->value('gender', 'F');
			$this->assertEqual('female', $result);
		}
		
		function testValueOfUnsupportedFields() {
			$unsupportedFields = array('nickname', 'dob', 'postcode', 'country', 'language', 'timezone');
			
			foreach($unsupportedFields as $field) {
				$result = $this->helper->value($field, '');
				$this->assertEqual('(not supported by NoseRub)', $result);
			}
		}
		
		function testValueOfInvalidField() {
			$result = $this->helper->value('invalidfield', 'a value');
			$this->assertEqual('(invalid)', $result);
		}
	}
?>