<?php

App::import('Vendor', 'UrlUtil');

class UrlUtilTest extends CakeTestCase {
	public function testStartsWithHttpOrHttps() {
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('http://example.com'));
		$this->assertIdentical(true, UrlUtil::startsWithHttpOrHttps('https://example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttpOrHttps('example.com'));
		$this->assertIdentical(false, UrlUtil::startsWithHttpOrHttps('example.com?url=http://example.net'));
	}
}