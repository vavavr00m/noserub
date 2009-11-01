<?php

class StatusesControllerTest extends CakeWebTestCase {
	private $baseUrl = null;
	
	public function setUp() {
		$this->baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/api/statuses/';		
	}
	
	public function testShowWithoutId() {
		$this->get($this->baseUrl.'show.xml');
		$this->assertNoStatusFound();
	}
	
	public function testShowWithInvalidId() {
		$this->get($this->baseUrl.'show/invalid.xml');
		$this->assertNoStatusFound();
	}
	
	public function testShowWithNonExistingId() {
		$this->get($this->baseUrl.'show/999999999999999999.xml');
		$this->assertNoStatusFound();
	}
	
	private function assertNoStatusFound() {
		$this->assertResponse(404);
		$this->assertPattern('#No status found with that ID.#');		
	}
}