<?php
App::import('Model', 'ContactType');

class ContactTypeTest extends CakeTestCase {
	private $model = null;
	
	public function setUp() {
		$this->model = new ContactType();
	}

	public function testGetContactTypesFromString() {
		$contactTypes = $this->model->getContactTypesFromString('');
		$this->assertIdentical(array(), $contactTypes);
		
		$contactTypes = $this->model->getContactTypesFromString('test');
		$this->assertEqual('test', $contactTypes[0]);
		
		$contactTypes = $this->model->getContactTypesFromString('<!test?>');
		$this->assertEqual('test', $contactTypes[0]);
		
		$contactTypes = $this->model->getContactTypesFromString('typeA typeB');
		$this->assertEqual('typeA', $contactTypes[0]);
		$this->assertEqual('typeB', $contactTypes[1]);
	}
	
	public function testGetContactTypesFromStringWithRemovingDuplicates() {
		$contactTypes = $this->model->getContactTypesFromString('test test');
		$this->assertEqual(1, count($contactTypes));
		$this->assertEqual('test', $contactTypes[0]);
		
		$contactTypes = $this->model->getContactTypesFromString('test TEST');
		$this->assertEqual(2, count($contactTypes));
		$this->assertEqual('test', $contactTypes[0]);
		$this->assertEqual('TEST', $contactTypes[1]);
	}
}