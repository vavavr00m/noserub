<?php

Mock::generatePartial('ContactType', 'ContactTypeTestVersion', array('findAll', 'create', 'save'));
Mock::generatePartial('Model', 'ModelForContactTypeTest', array('findAll'));

class ContactTypeTest extends CakeTestCase {
	private $model = null;
	
	function setUp() {
		$this->model = new ContactType();
	}
			
	function testGetContactTypesFromString() {
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
	
	function testGetContactTypesFromStringWithRemovingDuplicates() {
		$contactTypes = $this->model->getContactTypesFromString('test test');
		$this->assertEqual(1, count($contactTypes));
		$this->assertEqual('test', $contactTypes[0]);
		
		$contactTypes = $this->model->getContactTypesFromString('test TEST');
		$this->assertEqual(2, count($contactTypes));
		$this->assertEqual('test', $contactTypes[0]);
		$this->assertEqual('TEST', $contactTypes[1]);
	}
	
	function testGetIDsOfContactTypes() {
		$returnValue = array(0 => array('ContactType' => array('id' => 4)),
							 1 => array('ContactType' => array('id' => 7)));
							 
		$mock = new ContactTypeTestVersion($this);
		$mock->setReturnValue('findAll', $returnValue);
		
		$contactTypeIDs = $mock->getIDsOfContactTypes(1, array('typeA', 'typeB'));
		$this->assertEqual(4, $contactTypeIDs[0]);
		$this->assertEqual(7, $contactTypeIDs[1]);
	}
	
	function testGetIDsOfUnusedContactTypes() {
		$returnValue = array(0 => array('ContactTypesContact' => array('contact_type_id' => 3)), 
		                     1 => array('ContactTypesContact' => array('contact_type_id' => 4)));
		                     
		$mock = new ModelForContactTypeTest($this);
		$mock->setReturnValue('findAll', $returnValue);
		$this->model->ContactTypesContact = $mock;
		
		$contactTypeIDs = $this->model->getIDsOfUnusedContactTypes(array(3, 4));
		$this->assertIdentical(array(), $contactTypeIDs);
		
		$contactTypeIDs = $this->model->getIDsOfUnusedContactTypes(array(2, 3, 4, 5));
		$this->assertEqual(2, $contactTypeIDs[0]);
		$this->assertEqual(5, $contactTypeIDs[1]);
	}
	
	function testGetNewContactTypes() {
		$returnValue = array(0 => array('ContactType' => array('name' => 'typeA')), 
		                     1 => array('ContactType' => array('name' => 'typeB')));
		
		$mock = new ContactTypeTestVersion($this);
		$mock->setReturnValue('findAll', $returnValue);
		
		$newContactTypes = $mock->getNewContactTypes(1, array('typeA', 'typeB')); 
		$this->assertIdentical(array(), $newContactTypes);
		
		$newContactTypes = $mock->getNewContactTypes(1, array('typeA', 'typeC')); 
		$this->assertEqual(1, count($newContactTypes));
		$this->assertEqual('typeC', $newContactTypes[0]);
	}
	
	private function createDataForContactType($identityId, $name) {
		$data['ContactType']['identity_id'] = $identityId;
		$data['ContactType']['name'] = $name;
		
		return $data;
	}
}
?>