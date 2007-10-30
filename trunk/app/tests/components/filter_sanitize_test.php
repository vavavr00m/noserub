<?php

	class FilterSanitizeComponentTest extends CakeTestCase {
		private $component = null;
		
		function setUp() {
			$this->component = new FilterSanitizeComponent();
		}
		
		function testSanitize() {
			$existingFilters = array('photo', 'video', 'audio', 'link', 'text', 'event', 'micropublish', 'document', 'location');
			
			foreach ($existingFilters as $filter) {
				$this->assertEqual($filter, $this->component->sanitize($filter));
			}
		}
		
		function testSanitizeNotExistingFilter() {
			$this->assertFalse($this->component->sanitize('not_existing_filter'));
		}
	}
?>