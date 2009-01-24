<?php

App::import('Model', 'OmbService');

class OmbServiceTest extends CakeTestCase {
	private $service = null;
	
	public function setUp() {
		$this->service = new OmbService();
	}
}