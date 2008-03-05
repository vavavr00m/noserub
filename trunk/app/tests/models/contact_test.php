<?php
	Mock::generatePartial('Model', 'MockModel', array('create', 'save'));

	class ContactTest extends CakeTestCase {
		private $model;
		
		function setUp() {
			$this->model = new Contact();
		}
		
		function testCreateAssociationsToContactTypes() {
			$contactId = 1;
			$firstContactTypeId = 3;
			$secondContactTypeId = 4;
			$mock = new MockModel($this);
			
			$mock->expectAt(0, 'create', array($this->createDataForContactTypesContactTable($contactId, $firstContactTypeId)));
			$mock->expectAt(1, 'create', array($this->createDataForContactTypesContactTable($contactId, $secondContactTypeId)));
			$this->expectCallsToCreateAndSave($mock);
			
			$this->model->ContactTypesContact = $mock;
			$this->model->createAssociationsToContactTypes($contactId, array($firstContactTypeId, $secondContactTypeId));
		}
		
		function testCreateAssociationsToNoserubContactTypes() {
			$contactId = 1;
			$firstContactTypeId = 3;
			$secondContactTypeId = 4;
			$mock = new MockModel($this);
			
			$mock->expectAt(0, 'create', array($this->createDataForContactsNoserubContactTypeTable($contactId, $firstContactTypeId)));
			$mock->expectAt(1, 'create', array($this->createDataForContactsNoserubContactTypeTable($contactId, $secondContactTypeId)));
			$this->expectCallsToCreateAndSave($mock);
			
			$this->model->ContactsNoserubContactType = $mock;
			$this->model->createAssociationsToNoserubContactTypes($contactId, array($firstContactTypeId, $secondContactTypeId));
		}
		
		private function createDataForContactTypesContactTable($contactId, $contactTypeId) {
			$data['ContactTypesContact']['contact_id'] = $contactId;
			$data['ContactTypesContact']['contact_type_id'] = $contactTypeId;
			
			return $data;
		}
		
		private function createDataForContactsNoserubContactTypeTable($contactId, $contactTypeId) {
			$data['ContactsNoserubContactType']['contact_id'] = $contactId;
			$data['ContactsNoserubContactType']['noserub_contact_type_id'] = $contactTypeId;
			
			return $data;
		}
		
		private function expectCallsToCreateAndSave($mock) {
			$mock->expectCallCount('create', 2);
			$mock->expectCallCount('save', 2);
		}
	}
?>