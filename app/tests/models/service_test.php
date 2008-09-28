<?php

class ServiceTest extends CakeTestCase {
	private $service = null;
	
	public function setUp() {
		$this->service = new Service();
	}
	
	public function testDetectServiceFromInvalidString() {
		$this->assertIdentical(false, $this->service->detectService(''));
		$this->assertIdentical(false, $this->service->detectService(' '));
	}
	
	public function testDetect23hqService() {
		$result = $this->service->detectService('23hq.com/username');
		$this->assertService(4, 'username', $result);
	}
	
	public function testDetectAIMService() {
		$result = $this->service->detectService('aim:goIM?screenname=username');
		$this->assertService(27, 'username', $result);
	}
	
	public function testDetectBloggerdeService() {
		$result = $this->service->detectService('username.blogger.de');
		$this->assertService(52, 'username', $result);
		
		$result = $this->service->detectService('http://username.blogger.de');
		$this->assertService(52, 'username', $result);
	}
	
	public function testDetectCorkdService() {
		$result = $this->service->detectService('corkd.com/people/username');
		$this->assertService(15, 'username', $result);
	}
	
	public function testDetectDailymotionService() {
		$result = $this->service->detectService('dailymotion.com/username');
		$this->assertService(16, 'username', $result);
	}
	
	public function testDetectDeliciousService() {
		$result = $this->service->detectService('del.icio.us/username');
		$this->assertService(2, 'username', $result);
	}
	
	public function testDetectDeviantartService() {
		$result = $this->service->detectService('username.deviantart.com');
		$this->assertService(44, 'username', $result);
		
		$result = $this->service->detectService('http://username.deviantart.com');
		$this->assertService(44, 'username', $result);
	}
	
	public function testDetectDiggService() {
		$result = $this->service->detectService('digg.com/users/username');
		$this->assertService(38, 'username', $result);
	}
	
	public function testDetectDopplrService() {
		$result = $this->service->detectService('dopplr.com/traveller/username');
		$this->assertService(48, 'username', $result);
	}
	
	public function testDetectFacebookService() {
		$result = $this->service->detectService('facebook.com/profile.php?id=username');
		$this->assertService(30, 'username', $result);
	}
	
	public function testDetectFavesService() {
		$result = $this->service->detectService('faves.com/users/username');
		$this->assertService(42, 'username', $result);
	}
	
	public function testDetectFlickrService() {
		$result = $this->service->detectService('www.flickr.com/photos/username');
		$this->assertService(1, 'username', $result);
	}
	
	public function testDetectFolkdService() {
		$result = $this->service->detectService('folkd.com/user/username');
		$this->assertService(40, 'username', $result);
	}
	
	public function testDetectGadugaduService() {
		$result = $this->service->detectService('gg:username');
		$this->assertService(47, 'username', $result);
	}
	
	public function testDetectGtalkService() {
		$result = $this->service->detectService('xmpp:username@gmail.com');
		$this->assertService(24, 'username@gmail.com', $result);
		
		$result = $this->service->detectService('xmpp:username');
		$this->assertNotEqual(24, $result['service_id']);
	}
	
	public function testDetectIcqService() {
		$result = $this->service->detectService('icq.com/username');
		$this->assertService(25, 'username', $result);
	}
	
	public function testDetectIlikeService() {
		$result = $this->service->detectService('ilike.com/user/username');
		$this->assertService(19, 'username', $result);
	}
	
	public function testDetectImthereService() {
		$result = $this->service->detectService('imthere.com/users/username');
		$this->assertService(21, 'username', $result);
	}
	
	public function testDetectIpernityService() {
		$result = $this->service->detectService('ipernity.com/doc/username/home/photo');
		$this->assertService(3, 'username', $result);
	}
	
	public function testDetectJabberService() {
		$result = $this->service->detectService('xmpp:username');
		$this->assertService(23, 'username', $result);

		$result = $this->service->detectService('xmpp:username@gmail.com');
		$this->assertNotEqual(23, $result['service_id']);
	}
	
	public function testDetectKulandoService() {
		$result = $this->service->detectService('username.kulando.de');
		$this->assertService(50, 'username', $result);
		
		$result = $this->service->detectService('http://username.kulando.de');
		$this->assertService(50, 'username', $result);
	}
	
	public function testDetectLastfmService() {
		$result = $this->service->detectService('last.fm/user/username');
		$this->assertService(11, 'username', $result);
	}
	
	public function testDetectLinkedinService() {
		$result = $this->service->detectService('www.linkedin.com/in/username');
		$this->assertService(32, 'username', $result);
	}
	
	public function testDetectLivejournalService() {
		$result = $this->service->detectService('username.livejournal.com');
		$this->assertService(53, 'username', $result);
		
		$result = $this->service->detectService('http://username.livejournal.com');
		$this->assertService(53, 'username', $result);
	}
	
	public function testDetectMagnoliaService() {
		$result = $this->service->detectService('ma.gnolia.com/people/username');
		$this->assertService(13, 'username', $result);
	}
	
	public function testDetectMisterwongService() {
		$result = $this->service->detectService('mister-wong.de/user/username/?profile');
		$this->assertService(39, 'username', $result);
	}
	
	public function testDetectMoodmillService() {
		$result = $this->service->detectService('moodmill.com/citizen/username');
		$this->assertService(37, 'username', $result);
	}
	
	public function testDetectMsnService() {
		$result = $this->service->detectService('msnim:username');
		$this->assertService(29, 'username', $result);
	}
	
	public function testDetectNewsvineService() {
		$result = $this->service->detectService('username.newsvine.com');
		$this->assertService(22, 'username', $result);
		
		$result = $this->service->detectService('http://username.newsvine.com');
		$this->assertService(22, 'username', $result);
	}
	
	public function testDetectOdeoService() {
		$result = $this->service->detectService('odeo.com/profile/username');
		$this->assertService(18, 'username', $result);
	}
	
	public function testDetectOrkutService() {
		$result = $this->service->detectService('orkut.com/Profile.aspx?uid=username');
		$this->assertService(49, 'username', $result);
	}
	
	public function testDetectPlazesService() {
		$result = $this->service->detectService('plazes.com/users/username');
		$this->assertService(35, 'username', $result);
	}
	
	public function testDetectPownceService() {
		$result = $this->service->detectService('pownce.com/username');
		$this->assertService(6, 'username', $result);
	}
	
	public function testDetectQypeService() {
		$result = $this->service->detectService('qype.com/people/username');
		$this->assertService(12, 'username', $result);
	}
	
	public function testDetectRedditService() {
		$result = $this->service->detectService('reddit.com/user/username');
		$this->assertService(41, 'username', $result);
	}
	
	public function testDetectScribdService() {
		$result = $this->service->detectService('scribd.com/people/view/username');
		$this->assertService(36, 'username', $result);
	}
	
	public function testDetectSecondlifeService() {
		$result = $this->service->detectService('#username');
		$this->assertService(31, 'username', $result);
	}
	
	public function testDetectSimpyService() {
		$result = $this->service->detectService('simpy.com/user/username');
		$this->assertService(43, 'username', $result);
	}
	
	public function testDetectSkypeService() {
		$result = $this->service->detectService('skype:username');
		$this->assertService(28, 'username', $result);
	}
	
	public function testDetectSlideshareService() {
		$result = $this->service->detectService('slideshare.net/username');
		$this->assertService(34, 'username', $result);
	}
	
	public function testDetectStumbleuponService() {
		$result = $this->service->detectService('username.stumbleupon.com');
		$this->assertService(14, 'username', $result);
		
		$result = $this->service->detectService('http://username.stumbleupon.com');
		$this->assertService(14, 'username', $result);
	}
	
	public function testDetectTwitterService() {
		$result = $this->service->detectService('twitter.com/username');
		$this->assertService(5, 'username', $result);
	}
	
	public function testDetectUpcomingService() {
		$result = $this->service->detectService('upcoming.yahoo.com/user/username');
		$this->assertService(9, 'username', $result);
	}
	
	public function testDetectViddlerService() {
		$result = $this->service->detectService('viddler.com/explore/username');
		$this->assertService(45, 'username', $result);
	}
	
	public function testDetectViddyouService() {
		$result = $this->service->detectService('viddyou.com/profile.php?user=username');
		$this->assertService(46, 'username', $result);
	}
	
	public function testDetectVimeoService() {
		$result = $this->service->detectService('vimeo.com/username');
		$this->assertService(10, 'username', $result);
	}
	
	public function testDetectWeventService() {
		$result = $this->service->detectService('wevent.org/users/username');
		$this->assertService(20, 'username', $result);
	}
	
	public function testDetectWordpressService() {
		$result = $this->service->detectService('username.wordpress.com');
		$this->assertService(51, 'username', $result);
		
		$result = $this->service->detectService('http://username.wordpress.com');
		$this->assertService(51, 'username', $result);
	}
	
	public function testDetectXingService() {
		$result = $this->service->detectService('xing.com/profile/username');
		$this->assertService(33, 'username', $result);
	}
	
	public function testDetectYimService() {
		$result = $this->service->detectService('edit.yahoo.com/config/send_webmesg?.target=username&.src=pg');
		$this->assertService(26, 'username', $result);
	}
	
	public function testDetectZooomrService() {
		$result = $this->service->detectService('zooomr.com/photos/username');
		$this->assertService(17, 'username', $result);
	}
			
	private function assertService($expectedServiceId, $expectedUsername, $result) {
		$this->assertEqual($expectedServiceId, $result['service_id']);
		$this->assertEqual($expectedUsername, $result['username']);
	}
}
?>