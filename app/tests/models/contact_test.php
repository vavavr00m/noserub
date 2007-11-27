<?php

	class ContactTest extends CakeTestCase {
		private $model;
		
		function setUp() {
			$this->model = new Contact();
		}
		
		function testExtractIdsOfSelectedNoserubContactTypes() {
			$data = array('NoserubContactType' => array(1 => 1, 2 => 0, 3 => 1, 4 => 0));
			$ids = $this->model->extractIdsOfSelectedNoserubContactTypes($data);
			$this->assertEqual(2, count($ids));
			$this->assertEqual(1, $ids[0]);
			$this->assertEqual(3, $ids[1]);
		}
		
		function testExtractIdsOfSelectedNoserubContactTypesWithInvalidData() {
			$ids = $this->model->extractIdsOfSelectedNoserubContactTypes(array());
			$this->assertIdentical(true, empty($ids));
			
			$ids = $this->model->extractIdsOfSelectedNoserubContactTypes(array(1, 2, 3));
			$this->assertIdentical(true, empty($ids));			
		}
	}
?>