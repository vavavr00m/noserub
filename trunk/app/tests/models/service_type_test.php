<?php

class ServiceTypeTest extends CakeTestCase {
	private $model;
	private $existingFilters = array('photo', 'video', 'audio', 'link', 'text', 'micropublish', 'event', 'document', 'location', 'noserub');
	
	public function setUp() {
		$this->model = new ServiceType();
	}
	
	public function testSanitize() {
		foreach ($this->existingFilters as $filter) {
			$this->assertEqual($filter, $this->model->sanitizeFilter($filter));
		}
	}
	
	public function testSanitizeNotExistingFilter() {
		$this->assertFalse($this->model->sanitizeFilter('not_existing_filter'));
	}
	
	public function testGetFilters() {
	    $this->assertEqual($this->existingFilters, array_keys($this->model->getFilters()));
	}
}