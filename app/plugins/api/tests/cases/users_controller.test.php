<?php

class UsersControllerTest extends CakeWebTestCase {
	private $baseUrl = null;
	
	public function setUp() {
		$this->baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/api/users/';	
	}
	
	public function testShowWithoutId() {
		$this->get($this->baseUrl.'show.xml');
		$this->assertUserNotFound();
	}
	
	private function assertUserNotFound() {
		$this->assertResponse(404);
		$this->assertPattern('#Not found#');		
	}
}