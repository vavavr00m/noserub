<?php

App::import('Vendor', 'UrlUtil');

class UrlUtilTest extends CakeTestCase {
	public function testAddHttpIfNoProtocolSpecified() {
		$this->assertEqual('http://example.com', UrlUtil::addHttpIfNoProtocolSpecified('example.com'));
		$this->assertEqual('http://example.com', UrlUtil::addHttpIfNoProtocolSpecified('http://example.com'));
		$this->assertEqual('https://example.com', UrlUtil::addHttpIfNoProtocolSpecified('https://example.com'));
		$this->assertEqual('', UrlUtil::addHttpIfNoProtocolSpecified(''));
	}
	
	public function testStartsWithHttpOrHttps() {
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('http://example.com'));
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('https://example.com'));
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('HTTP://example.com'));
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('HTTPS://example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttpOrHttps('example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttpOrHttps('example.com?url=http://example.net'));
	}
}