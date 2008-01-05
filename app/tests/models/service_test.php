<?php

	class ServiceTest extends CakeTestCase {
		private $service = null;
		
		function setUp() {
			$this->service = new Service();
		}
		
		function testDetectServiceFromInvalidString() {
			$this->assertIdentical(false, $this->service->detectService(''));
			$this->assertIdentical(false, $this->service->detectService(' '));
		}
		
		function testDetect23hqService() {
			$result = $this->service->detectService('23hq.com/username');
			$this->assertService(4, 'username', $result);
		}
		
		function testDetectAIMService() {
			$result = $this->service->detectService('aim:goIM?screenname=username');
			$this->assertService(27, 'username', $result);
		}
		
		function testDetectDeliciousService() {
			$result = $this->service->detectService('del.icio.us/username');
			$this->assertService(2, 'username', $result);
		}
		
		function testDetectFlickrService() {
			$result = $this->service->detectService('www.flickr.com/photos/username');
			$this->assertService(1, 'username', $result);
		}
		
		function testDetectGadugaduService() {
			$result = $this->service->detectService('gg:username');
			$this->assertService(47, 'username', $result);
		}
		
		function testDetectIpernityService() {
			$result = $this->service->detectService('ipernity.com/doc/username/home/photo');
			$this->assertService(3, 'username', $result);
		}
		
		function testDetectMsnService() {
			$result = $this->service->detectService('msnim:username');
			$this->assertService(29, 'username', $result);
		}
		
		function testDetectPownceService() {
			$result = $this->service->detectService('pownce.com/username');
			$this->assertService(6, 'username', $result);
		}
		
		function testDetectSkypeService() {
			$result = $this->service->detectService('skype:username');
			$this->assertService(28, 'username', $result);
		}
		
		function testDetectTwitterService() {
			$result = $this->service->detectService('twitter.com/username');
			$this->assertService(5, 'username', $result);
		}
		
		function testDetectUpcomingService() {
			$result = $this->service->detectService('upcoming.yahoo.com/user/username');
			$this->assertService(9, 'username', $result);
		}
		
		function testDetectVimeoService() {
			$result = $this->service->detectService('vimeo.com/username');
			$this->assertService(10, 'username', $result);
		}
		
		function testGetDomainFromString() {
			$this->assertEqual('example.com', $this->service->getDomainFromString('http://example.com'));
			$this->assertEqual('example.com', $this->service->getDomainFromString('https://example.com'));
			$this->assertEqual('example.com', $this->service->getDomainFromString('www.example.com'));
			$this->assertEqual('example.com', $this->service->getDomainFromString('http://example.com/example'));
			$this->assertEqual('example.com', $this->service->getDomainFromString('http://subdomain.example.com'));
			$this->assertEqual('example.com', $this->service->getDomainFromString('http://subsubdomain.subdomain.example.com'));
		}
		
		function testGetDomainFromStringWithoutDomain() {
			$this->assertIdentical(false, $this->service->getDomainFromString(''));
			$this->assertIdentical(false, $this->service->getDomainFromString('example'));
			$this->assertIdentical(false, $this->service->getDomainFromString('e.x'));
		}
		
		private function assertService($expectedServiceId, $expectedUsername, $result) {
			$this->assertEqual($expectedServiceId, $result['service_id']);
			$this->assertEqual($expectedUsername, $result['username']);
		}
	}
?>