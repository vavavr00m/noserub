<?php

	class MenuitemTest extends CakeTestCase {
		
		function testMenuItem() {
			$label = 'A menu item';
			$link = '/test';
			$isActive = true;
			
			$menuItem = new MenuItem($label, $link, $isActive);
			$this->assertEqual($label, $menuItem->getLabel());
			$this->assertEqual($link, $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
?>