<?php

App::import('Vendor', 'UrlUtil');

class UrlUtilTest extends CakeTestCase {
	public function testAddHttpIfNoProtocolSpecified() {
		$this->assertEqual('http://example.com', UrlUtil::addHttpIfNoProtocolSpecified('example.com'));
		$this->assertEqual('http://example.com', UrlUtil::addHttpIfNoProtocolSpecified('http://example.com'));
		$this->assertEqual('https://example.com', UrlUtil::addHttpIfNoProtocolSpecified('https://example.com'));
		$this->assertEqual('', UrlUtil::addHttpIfNoProtocolSpecified(''));
	}

	public function testRemoveHttpAndHttps() {
		$this->assertEqual('example.com', UrlUtil::removeHttpAndHttps('example.com'));
		$this->assertEqual('example.com', UrlUtil::removeHttpAndHttps('http://example.com'));
		$this->assertEqual('example.com', UrlUtil::removeHttpAndHttps('https://example.com'));
		$this->assertEqual('example.com', UrlUtil::removeHttpAndHttps('HTTP://example.com'));
		$this->assertEqual('example.com', UrlUtil::removeHttpAndHttps('HTTPS://example.com'));
		$this->assertEqual('example.com?url=http://example.com', UrlUtil::removeHttpAndHttps('http://example.com?url=http://example.com'));
		$this->assertEqual('example.com?url=https://example.com', UrlUtil::removeHttpAndHttps('https://example.com?url=https://example.com'));
	}
	
	public function testStartsWithHttp() {
		$this->assertIdentical(true, UrlUtil::startsWithHttp('http://example.com'));
		$this->assertIdentical(true, UrlUtil::startsWithHttp('HTTP://example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttp('https://example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttp('example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttp('example.com?url=http://example.net'));
	}
	
	public function testStartsWithHttpOrHttps() {
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('http://example.com'));
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('https://example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttpOrHttps('example.com'));
	}
	
	public function testStartsWithHttps() {
		$this->assertIdentical(true, UrlUtil::startsWithHttps('https://example.com'));
		$this->assertIdentical(true, UrlUtil::startsWithHttps('HTTPS://example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttps('http://example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttps('example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttps('example.com?url=https://example.net'));
	}
}