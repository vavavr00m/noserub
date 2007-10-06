<?php

	class NiceSRegTest extends CakeTestCase {
		var $helper = null;
		var $supportedFields = array('email', 'fullname', 'gender');
		var $unsupportedFields = array('nickname', 'dob', 'postcode', 'country', 'language', 'timezone');
		
		function setUp() {
			$this->helper = new NiceSRegHelper();
			loadHelper('Html');
			loadHelper('Form');
			$this->helper->Form = new FormHelper();
			$this->helper->Form->Html = new HtmlHelper();
		}
		
		// FIXME this test will break with a newer version of CakePHP due to a bug in the currently used version!
		function testCheckboxForSupportedFields() {
			foreach($this->supportedFields as $field) {
				$result = $this->helper->checkboxForSupportedFields($field);		
				$expected = '<input type="hidden" name="data[OpenidSite]['.$field.']" value="0" id="OpenidSite'.ucfirst($field).'_" />' .
							'<input type="checkbox" name="data[OpenidSite]['.$field.']" type="checkbox" checked="checked" value="1" id="OpenidSite'.ucfirst($field).'" />';
				$this->assertEqual($expected, $result);
			}
		}
		
		// FIXME this test will break with a newer version of CakePHP due to a bug in the currently used version
		function testCheckboxForSupportedFieldsWithOpenidSiteData() {
			$data = array('OpenidSite' => array('email' => 1));
			$result = $this->helper->checkboxForSupportedFields('email', $data);		
			$expected = '<input type="hidden" name="data[OpenidSite][email]" value="0" id="OpenidSiteEmail_" />' .
						'<input type="checkbox" name="data[OpenidSite][email]" type="checkbox" checked="checked" value="1" id="OpenidSiteEmail" />';
			$this->assertEqual($expected, $result);
			
			$data = array('OpenidSite' => array('email' => 0));
			$result = $this->helper->checkboxForSupportedFields('email', $data);
			$expected = '<input type="hidden" name="data[OpenidSite][email]" value="0" id="OpenidSiteEmail_" />' .
						'<input type="checkbox" name="data[OpenidSite][email]" type="checkbox"  value="1" id="OpenidSiteEmail" />';
			$this->assertEqual($expected, $result);
		}
		
		function testCheckboxForSupportedFieldsWithUnsupportedFields() {
			foreach($this->unsupportedFields as $field) {
				$result = $this->helper->checkboxForSupportedFields($field);
				$this->assertEqual('', $result);
			}
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
			foreach($this->unsupportedFields as $field) {
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