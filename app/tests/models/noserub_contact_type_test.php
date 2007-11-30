<?php

	class NoserubContactTypeTest extends CakeTestCase {
		private $model;
		
		function setUp() {
			$this->model = new NoserubContactType();
		}
		
		function testGetSelectedNoserubContactTypeIDs() {
			$data = array('NoserubContactType' => array(1 => 0, 2 => 0, 3 => 0, 4 => 0));
			$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs($data);
			$this->assertIdentical(true, empty($noserubContactTypeIDs));
			
			$data = array('NoserubContactType' => array(1 => 1, 2 => 0, 3 => 1, 4 => 0));
			$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs($data);
			$this->assertEqual(2, count($noserubContactTypeIDs));
			$this->assertEqual(1, $noserubContactTypeIDs[0]);
			$this->assertEqual(3, $noserubContactTypeIDs[1]);
		}
		
		function testExtractIdsOfSelectedNoserubContactTypesWithInvalidData() {
			$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs(array());
			$this->assertIdentical(true, empty($noserubContactTypeIDs));
			
			$noserubContactTypeIDs = $this->model->getSelectedNoserubContactTypeIDs(array(1, 2, 3));
			$this->assertIdentical(true, empty($noserubContactTypeIDs));			
		}
	}
?>