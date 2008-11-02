<?php
App::import('Model', 'NoserubContactType');

Mock::generatePartial('Model', 'NoserubContactTypeTestVersion', array('findAllByContactId'));

class NoserubContactTypeTest extends CakeTestCase {
	private $model;
	
	public function setUp() {
		$this->model = new NoserubContactType();
	}

	public function skip() {
		// TODO fix tests
		$this->skipIf(true, 'The Containable behavior causes an error when running those tests');
	}
	
	public function testGetNoserubContactTypeIDsForContact() {
		$returnValue = array(0 => array('ContactsNoserubContactType' => array('noserub_contact_type_id' => 4)),
							 1 => array('ContactsNoserubContactType' => array('noserub_contact_type_id' => 7)));
							 
		$mock = new NoserubContactTypeTestVersion($this);
		$mock->setReturnValue('findAllByContactId', $returnValue);
		
		$this->model->ContactsNoserubContactType = $mock;
		$noserub_contact_type_ids = $this->model->getIDsForContact(1);
		$this->assertEqual(4, $noserub_contact_type_ids[0]);
		$this->assertEqual(7, $noserub_contact_type_ids[1]);
	}
	
	public function testGetNoserubContactTypeIDsToAdd() {
		$old = array(1, 2, 3);
		$new = array(1, 2, 3);
		$this->assertIdentical(array(), $this->model->getNoserubContactTypeIDsToAdd($old, $new));
		
		$new = array(1, 4, 5);
		$this->assertIdentical(array(4, 5), $this->model->getNoserubContactTypeIDsToAdd($old, $new));
	}
	
	public function testGetNoserubContactTypeIDsToRemove() {
		$old = array(1, 2, 3);
		$new = array(1, 2, 3);
		$this->assertIdentical(array(), $this->model->getNoserubContactTypeIDsToRemove($old, $new));
		
		$new = array(2);
		$this->assertIdentical(array(1, 3), $this->model->getNoserubContactTypeIDsToRemove($old, $new));
	}
	
	public function testGetSelectedNoserubContactTypeIDs() {
		$data = array('NoserubContactType' => array(1 => 0, 2 => 0, 3 => 0, 4 => 0));
		$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs($data);
		$this->assertIdentical(true, empty($noserubContactTypeIDs));
		
		$data = array('NoserubContactType' => array(1 => 1, 2 => 0, 3 => 1, 4 => 0));
		$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs($data);
		$this->assertEqual(2, count($noserubContactTypeIDs));
		$this->assertEqual(1, $noserubContactTypeIDs[0]);
		$this->assertEqual(3, $noserubContactTypeIDs[1]);
	}
	
	public function testGetSelectedNoserubContactTypeIDsFromInvalidData() {
		$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs(array());
		$this->assertIdentical(true, empty($noserubContactTypeIDs));
		
		$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs(array(1, 2, 3));
		$this->assertIdentical(true, empty($noserubContactTypeIDs));			
	}
}
?>