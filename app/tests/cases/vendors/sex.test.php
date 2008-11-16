<?php

App::import('Vendor', 'Sex');

class SexTest extends CakeTestCase {
	public function testGetImageUrl() {
		$baseUrl = Router::url(Sex::AVATAR_PATH, true);
		$this->assertEqual($baseUrl.'male.gif', Sex::getImageUrl(Sex::MALE));
		$this->assertEqual($baseUrl.'female.gif', Sex::getImageUrl(Sex::FEMALE));
		$this->assertEqual($baseUrl.'noinfo.gif', Sex::getImageUrl(Sex::UNDEFINED));
	}
	
	public function testGetSmallImageUrl() {
		$baseUrl = Router::url(Sex::AVATAR_PATH, true);
		$this->assertEqual($baseUrl.'male-small.gif', Sex::getSmallImageUrl(Sex::MALE));
		$this->assertEqual($baseUrl.'female-small.gif', Sex::getSmallImageUrl(Sex::FEMALE));
		$this->assertEqual($baseUrl.'noinfo-small.gif', Sex::getSmallImageUrl(Sex::UNDEFINED));
	}
	
	public function testHeOrShe() {
		$this->assertEqual('he', Sex::heOrShe(Sex::MALE));
		$this->assertEqual('she', Sex::heOrShe(Sex::FEMALE));
		$this->assertEqual('he/she', Sex::heOrShe(Sex::UNDEFINED));
	}
	
	public function testHimOrHer() {
		$this->assertEqual('him', Sex::himOrHer(Sex::MALE));
		$this->assertEqual('her', Sex::himOrHer(Sex::FEMALE));
		$this->assertEqual('him/her', Sex::himOrHer(Sex::UNDEFINED));
	}
	
	public function testHisOrHer() {
		$this->assertEqual('his', Sex::hisOrHer(Sex::MALE));
		$this->assertEqual('her', Sex::hisOrHer(Sex::FEMALE));
		$this->assertEqual('his/her', Sex::hisOrHer(Sex::UNDEFINED));
	}
}