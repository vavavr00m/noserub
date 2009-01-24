<?php

App::import('Model', 'OmbLocalService');

class OmbLocalServiceTest extends CakeTestCase {
	private $service = null;
	
	public function setUp() {
		$this->service = new OmbLocalService();
	}
}