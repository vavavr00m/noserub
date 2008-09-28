<?php

class NiceSRegTest extends CakeTestCase {
	private $helper = null;
	private $supportedFields = array('email', 'fullname', 'gender');
	private $unsupportedFields = array('nickname', 'dob', 'postcode', 'country', 'language', 'timezone');
	
	public function setUp() {
		// putting a view object to the class registry so that the form helper works
		ClassRegistry::removeObject('view');
		new View(new AppController());
		
		$this->helper = new NiceSRegHelper();
		App::import('Helper', 'Html');
		App::import('Helper', 'Form');			
		$this->helper->Form = new FormHelper();
		$this->helper->Form->Html = new HtmlHelper();
	}

	public function testCheckboxForSupportedFields() {
		foreach($this->supportedFields as $field) {
			$result = $this->helper->checkboxForSupportedFields($field);		
			$expected = '<input type="hidden" name="data[OpenidSite]['.$field.']" value="0" id="OpenidSite'.ucfirst($field).'_" />' .
						'<input type="checkbox" name="data[OpenidSite]['.$field.']" checked="checked" value="1" id="OpenidSite'.ucfirst($field).'" />';
			$this->assertEqual($expected, $result);
		}
	}
	
	public function testCheckboxForSupportedFieldsWithOpenidSiteData() {
		$data = array('OpenidSite' => array('email' => 1));
		$result = $this->helper->checkboxForSupportedFields('email', $data);		
		$expected = '<input type="hidden" name="data[OpenidSite][email]" value="0" id="OpenidSiteEmail_" />' .
					'<input type="checkbox" name="data[OpenidSite][email]" checked="checked" value="1" id="OpenidSiteEmail" />';
		$this->assertEqual($expected, $result);
		// FIXME this assert will probably fail in later releases due to an additional space in front of [value="1"]
		$data = array('OpenidSite' => array('email' => 0));
		$result = $this->helper->checkboxForSupportedFields('email', $data);
		$expected = '<input type="hidden" name="data[OpenidSite][email]" value="0" id="OpenidSiteEmail_" />' .
					'<input type="checkbox" name="data[OpenidSite][email]"  value="1" id="OpenidSiteEmail" />';
		$this->assertEqual($expected, $result);
	}
	
	public function testCheckboxForSupportedFieldsWithUnsupportedFields() {
		foreach($this->unsupportedFields as $field) {
			$result = $this->helper->checkboxForSupportedFields($field);
			$this->assertEqual('', $result);
		}
	}
	
	public function testKey() {
		$result = $this->helper->key('email');
		$this->assertEqual('Email', $result);
		
		$result = $this->helper->key('dob');
		$this->assertEqual('Date of birth', $result);
	}
	
	public function testValue() {
		$result = $this->helper->value('email', 'test@example.com');
		$this->assertEqual('test@example.com', $result);
		
		$result = $this->helper->value('fullname', 'Hans Muster');
		$this->assertEqual('Hans Muster', $result);
		
		$result = $this->helper->value('gender', 'M');
		$this->assertEqual('male', $result);
		
		$result = $this->helper->value('gender', 'F');
		$this->assertEqual('female', $result);
	}
	
	public function testValueOfUnsupportedFields() {
		foreach($this->unsupportedFields as $field) {
			$result = $this->helper->value($field, '');
			$this->assertEqual('(not supported by NoseRub)', $result);
		}
	}
	
	public function testValueOfInvalidField() {
		$result = $this->helper->value('invalidfield', 'a value');
		$this->assertEqual('(invalid)', $result);
	}
}