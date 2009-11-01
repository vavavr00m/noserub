<?php 

class HelpControllerTest extends CakeWebTestCase {
	private $baseUrl = null;
	
	public function setUp() {
		$this->baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/api/help/';		
	}
	
	public function testTestXML() {
		$this->get($this->baseUrl.'test.xml');
		$this->assertPattern('#<ok>true</ok>#');
	}
	
	public function testTestJson() {
		$this->get($this->baseUrl.'test.json');
		$this->assertPattern('/"ok"/');
	}
}