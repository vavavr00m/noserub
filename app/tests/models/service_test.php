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
		
		function testDetectBloggerdeService() {
			$result = $this->service->detectService('username.blogger.de');
			$this->assertService(52, 'username', $result);
			
			$result = $this->service->detectService('http://username.blogger.de');
			$this->assertService(52, 'username', $result);
		}
		
		function testDetectCorkdService() {
			$result = $this->service->detectService('corkd.com/people/username');
			$this->assertService(15, 'username', $result);
		}
		
		function testDetectDailymotionService() {
			$result = $this->service->detectService('dailymotion.com/username');
			$this->assertService(16, 'username', $result);
		}
		
		function testDetectDeliciousService() {
			$result = $this->service->detectService('del.icio.us/username');
			$this->assertService(2, 'username', $result);
		}
		
		function testDetectDeviantartService() {
			$result = $this->service->detectService('username.deviantart.com');
			$this->assertService(44, 'username', $result);
			
			$result = $this->service->detectService('http://username.deviantart.com');
			$this->assertService(44, 'username', $result);
		}
		
		function testDetectDiggService() {
			$result = $this->service->detectService('digg.com/users/username');
			$this->assertService(38, 'username', $result);
		}
		
		function testDetectDopplrService() {
			$result = $this->service->detectService('dopplr.com/traveller/username');
			$this->assertService(48, 'username', $result);
		}
		
		function testDetectFacebookService() {
			$result = $this->service->detectService('facebook.com/profile.php?id=username');
			$this->assertService(30, 'username', $result);
		}
		
		function testDetectFavesService() {
			$result = $this->service->detectService('faves.com/users/username');
			$this->assertService(42, 'username', $result);
		}
		
		function testDetectFlickrService() {
			$result = $this->service->detectService('www.flickr.com/photos/username');
			$this->assertService(1, 'username', $result);
		}
		
		function testDetectFolkdService() {
			$result = $this->service->detectService('folkd.com/user/username');
			$this->assertService(40, 'username', $result);
		}
		
		function testDetectGadugaduService() {
			$result = $this->service->detectService('gg:username');
			$this->assertService(47, 'username', $result);
		}
		
		function testDetectGtalkService() {
			$result = $this->service->detectService('xmpp:username@gmail.com');
			$this->assertService(24, 'username@gmail.com', $result);
			
			$result = $this->service->detectService('xmpp:username');
			$this->assertNotEqual(24, $result['service_id']);
		}
		
		function testDetectIcqService() {
			$result = $this->service->detectService('icq.com/username');
			$this->assertService(25, 'username', $result);
		}
		
		function testDetectIlikeService() {
			$result = $this->service->detectService('ilike.com/user/username');
			$this->assertService(19, 'username', $result);
		}
		
		function testDetectImthereService() {
			$result = $this->service->detectService('imthere.com/users/username');
			$this->assertService(21, 'username', $result);
		}
		
		function testDetectIpernityService() {
			$result = $this->service->detectService('ipernity.com/doc/username/home/photo');
			$this->assertService(3, 'username', $result);
		}
		
		function testDetectJabberService() {
			$result = $this->service->detectService('xmpp:username');
			$this->assertService(23, 'username', $result);

			$result = $this->service->detectService('xmpp:username@gmail.com');
			$this->assertNotEqual(23, $result['service_id']);
		}
		
		function testDetectKulandoService() {
			$result = $this->service->detectService('username.kulando.de');
			$this->assertService(50, 'username', $result);
			
			$result = $this->service->detectService('http://username.kulando.de');
			$this->assertService(50, 'username', $result);
		}
		
		function testDetectLastfmService() {
			$result = $this->service->detectService('last.fm/user/username');
			$this->assertService(11, 'username', $result);
		}
		
		function testDetectLinkedinService() {
			$result = $this->service->detectService('www.linkedin.com/in/username');
			$this->assertService(32, 'username', $result);
		}
		
		function testDetectLivejournalService() {
			$result = $this->service->detectService('username.livejournal.com');
			$this->assertService(53, 'username', $result);
			
			$result = $this->service->detectService('http://username.livejournal.com');
			$this->assertService(53, 'username', $result);
		}
		
		function testDetectMagnoliaService() {
			$result = $this->service->detectService('ma.gnolia.com/people/username');
			$this->assertService(13, 'username', $result);
		}
		
		function testDetectMisterwongService() {
			$result = $this->service->detectService('mister-wong.de/user/username/?profile');
			$this->assertService(39, 'username', $result);
		}
		
		function testDetectMoodmillService() {
			$result = $this->service->detectService('moodmill.com/citizen/username');
			$this->assertService(37, 'username', $result);
		}
		
		function testDetectMsnService() {
			$result = $this->service->detectService('msnim:username');
			$this->assertService(29, 'username', $result);
		}
		
		function testDetectNewsvineService() {
			$result = $this->service->detectService('username.newsvine.com');
			$this->assertService(22, 'username', $result);
			
			$result = $this->service->detectService('http://username.newsvine.com');
			$this->assertService(22, 'username', $result);
		}
		
		function testDetectOdeoService() {
			$result = $this->service->detectService('odeo.com/profile/username');
			$this->assertService(18, 'username', $result);
		}
		
		function testDetectOrkutService() {
			$result = $this->service->detectService('orkut.com/Profile.aspx?uid=username');
			$this->assertService(49, 'username', $result);
		}
		
		function testDetectPlazesService() {
			$result = $this->service->detectService('plazes.com/users/username');
			$this->assertService(35, 'username', $result);
		}
		
		function testDetectPownceService() {
			$result = $this->service->detectService('pownce.com/username');
			$this->assertService(6, 'username', $result);
		}
		
		function testDetectQypeService() {
			$result = $this->service->detectService('qype.com/people/username');
			$this->assertService(12, 'username', $result);
		}
		
		function testDetectRedditService() {
			$result = $this->service->detectService('reddit.com/user/username');
			$this->assertService(41, 'username', $result);
		}
		
		function testDetectScribdService() {
			$result = $this->service->detectService('scribd.com/people/view/username');
			$this->assertService(36, 'username', $result);
		}
		
		function testDetectSecondlifeService() {
			$result = $this->service->detectService('#username');
			$this->assertService(31, 'username', $result);
		}
		
		function testDetectSimpyService() {
			$result = $this->service->detectService('simpy.com/user/username');
			$this->assertService(43, 'username', $result);
		}
		
		function testDetectSkypeService() {
			$result = $this->service->detectService('skype:username');
			$this->assertService(28, 'username', $result);
		}
		
		function testDetectSlideshareService() {
			$result = $this->service->detectService('slideshare.net/username');
			$this->assertService(34, 'username', $result);
		}
		
		function testDetectStumbleuponService() {
			$result = $this->service->detectService('username.stumbleupon.com');
			$this->assertService(14, 'username', $result);
			
			$result = $this->service->detectService('http://username.stumbleupon.com');
			$this->assertService(14, 'username', $result);
		}
		
		function testDetectTwitterService() {
			$result = $this->service->detectService('twitter.com/username');
			$this->assertService(5, 'username', $result);
		}
		
		function testDetectUpcomingService() {
			$result = $this->service->detectService('upcoming.yahoo.com/user/username');
			$this->assertService(9, 'username', $result);
		}
		
		function testDetectViddlerService() {
			$result = $this->service->detectService('viddler.com/explore/username');
			$this->assertService(45, 'username', $result);
		}
		
		function testDetectViddyouService() {
			$result = $this->service->detectService('viddyou.com/profile.php?user=username');
			$this->assertService(46, 'username', $result);
		}
		
		function testDetectVimeoService() {
			$result = $this->service->detectService('vimeo.com/username');
			$this->assertService(10, 'username', $result);
		}
		
		function testDetectWeventService() {
			$result = $this->service->detectService('wevent.org/users/username');
			$this->assertService(20, 'username', $result);
		}
		
		function testDetectWordpressService() {
			$result = $this->service->detectService('username.wordpress.com');
			$this->assertService(51, 'username', $result);
			
			$result = $this->service->detectService('http://username.wordpress.com');
			$this->assertService(51, 'username', $result);
		}
		
		function testDetectXingService() {
			$result = $this->service->detectService('xing.com/profile/username');
			$this->assertService(33, 'username', $result);
		}
		
		function testDetectYimService() {
			$result = $this->service->detectService('edit.yahoo.com/config/send_webmesg?.target=username&.src=pg');
			$this->assertService(26, 'username', $result);
		}
		
		function testDetectZooomrService() {
			$result = $this->service->detectService('zooomr.com/photos/username');
			$this->assertService(17, 'username', $result);
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