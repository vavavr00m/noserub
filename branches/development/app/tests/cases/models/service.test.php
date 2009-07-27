<?php
App::import('Model', 'Service');

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
		$this->assertService('_23hq', 'username', $result);
	}
	
	public function testDetectAIMService() {
		$result = $this->service->detectService('aim:goIM?screenname=username');
		$this->assertService('Aim', 'username', $result);
	}
	
	public function testDetectBloggerdeService() {
		$result = $this->service->detectService('username.blogger.de');
		$this->assertService('Bloggerde', 'username', $result);
		
		$result = $this->service->detectService('http://username.blogger.de');
		$this->assertService('Bloggerde', 'username', $result);
	}
	
	public function testDetectCorkdService() {
		$result = $this->service->detectService('corkd.com/people/username');
		$this->assertService('Corkd', 'username', $result);
	}
	
	public function testDetectDailymotionService() {
		$result = $this->service->detectService('dailymotion.com/username');
		$this->assertService('Dailymotion', 'username', $result);
	}
	
	public function testDetectDeliciousService() {
		$result = $this->service->detectService('del.icio.us/username');
		$this->assertService('Delicious', 'username', $result);
		
		$result = $this->service->detectService('delicious.com/username');
		$this->assertService('Delicious', 'username', $result);
	}
	
	public function testDetectDeviantartService() {
		$result = $this->service->detectService('username.deviantart.com');
		$this->assertService('Deviantart', 'username', $result);
		
		$result = $this->service->detectService('http://username.deviantart.com');
		$this->assertService('Deviantart', 'username', $result);
	}
	
	public function testDetectDiggService() {
		$result = $this->service->detectService('digg.com/users/username');
		$this->assertService('Digg', 'username', $result);
	}
	
	public function testDetectDopplrService() {
		$result = $this->service->detectService('dopplr.com/traveller/username');
		$this->assertService('Dopplr', 'username', $result);
	}
	
	public function testDetectFacebookService() {
		$result = $this->service->detectService('facebook.com/profile.php?id=username');
		$this->assertService('Facebook', 'username', $result);
	}
	
	public function testDetectFavesService() {
		$result = $this->service->detectService('faves.com/users/username');
		$this->assertService('Faves', 'username', $result);
	}
	
	public function testDetectFlickrService() {
		$result = $this->service->detectService('www.flickr.com/photos/username');
		$this->assertService('Flickr', 'username', $result);
	}
	
	public function testDetectFolkdService() {
		$result = $this->service->detectService('folkd.com/user/username');
		$this->assertService('Folkd', 'username', $result);
	}
	
	public function testDetectGadugaduService() {
		$result = $this->service->detectService('gg:username');
		$this->assertService('Gadugadu', 'username', $result);
	}
	
	public function testDetectGtalkService() {
		$result = $this->service->detectService('xmpp:username@gmail.com');
		$this->assertService('Gtalk', 'username@gmail.com', $result);
		
		$result = $this->service->detectService('xmpp:username');
		$this->assertNotEqual('Gtalk', $result['service']);
	}
	
	public function testDetectIcqService() {
		$result = $this->service->detectService('icq.com/username');
		$this->assertService('Icq', 'username', $result);
	}
	
	public function testDetectIlikeService() {
		$result = $this->service->detectService('ilike.com/user/username');
		$this->assertService('Ilike', 'username', $result);
	}
	
	public function testDetectImthereService() {
		$result = $this->service->detectService('imthere.com/users/username');
		$this->assertService('Imthere', 'username', $result);
	}
	
	public function testDetectIpernityService() {
		$result = $this->service->detectService('ipernity.com/doc/username/home/photo');
		$this->assertService('Ipernity', 'username', $result);
	}
	
	public function testDetectJabberService() {
		$result = $this->service->detectService('xmpp:username');
		$this->assertService('Jabber', 'username', $result);

		$result = $this->service->detectService('xmpp:username@gmail.com');
		$this->assertNotEqual('Jabber', $result['service']);
	}
	
	public function testDetectKulandoService() {
		$result = $this->service->detectService('username.kulando.de');
		$this->assertService('Kulando', 'username', $result);
		
		$result = $this->service->detectService('http://username.kulando.de');
		$this->assertService('Kulando', 'username', $result);
	}
	
	public function testDetectLastfmService() {
		$result = $this->service->detectService('last.fm/user/username');
		$this->assertService('Lastfm', 'username', $result);
	}
	
	public function testDetectLinkedinService() {
		$result = $this->service->detectService('www.linkedin.com/in/username');
		$this->assertService('Linkedin', 'username', $result);
	}
	
	public function testDetectLivejournalService() {
		$result = $this->service->detectService('username.livejournal.com');
		$this->assertService('Livejournal', 'username', $result);
		
		$result = $this->service->detectService('http://username.livejournal.com');
		$this->assertService('Livejournal', 'username', $result);
	}
	
	public function testDetectMisterwongService() {
		$result = $this->service->detectService('mister-wong.de/user/username/?profile');
		$this->assertService('Misterwong', 'username', $result);
	}
	
	public function testDetectMoodmillService() {
		$result = $this->service->detectService('moodmill.com/citizen/username');
		$this->assertService('Moodmill', 'username', $result);
	}
	
	public function testDetectMsnService() {
		$result = $this->service->detectService('msnim:username');
		$this->assertService('Msn', 'username', $result);
	}
	
	public function testDetectNewsvineService() {
		$result = $this->service->detectService('username.newsvine.com');
		$this->assertService('Newsvine', 'username', $result);
		
		$result = $this->service->detectService('http://username.newsvine.com');
		$this->assertService('Newsvine', 'username', $result);
	}
	
	public function testDetectOdeoService() {
		$result = $this->service->detectService('odeo.com/profile/username');
		$this->assertService('Odeo', 'username', $result);
	}
	
	public function testDetectOrkutService() {
		$result = $this->service->detectService('orkut.com/Profile.aspx?uid=username');
		$this->assertService('Orkut', 'username', $result);
	}
	
	public function testDetectPlazesService() {
		$result = $this->service->detectService('plazes.com/users/username');
		$this->assertService('Plazes', 'username', $result);
	}
	
	public function testDetectQypeService() {
		$result = $this->service->detectService('qype.com/people/username');
		$this->assertService('Qype', 'username', $result);
	}
	
	public function testDetectRedditService() {
		$result = $this->service->detectService('reddit.com/user/username');
		$this->assertService('Reddit', 'username', $result);
	}
	
	public function testDetectScribdService() {
		$result = $this->service->detectService('scribd.com/people/view/username');
		$this->assertService('Scribd', 'username', $result);
	}
	
	public function testDetectSecondlifeService() {
		$result = $this->service->detectService('#username');
		$this->assertService('Secondlife', 'username', $result);
	}
	
	public function testDetectSimpyService() {
		$result = $this->service->detectService('simpy.com/user/username');
		$this->assertService('Simpy', 'username', $result);
	}
	
	public function testDetectSkypeService() {
		$result = $this->service->detectService('skype:username');
		$this->assertService('Skype', 'username', $result);
	}
	
	public function testDetectSlideshareService() {
		$result = $this->service->detectService('slideshare.net/username');
		$this->assertService('Slideshare', 'username', $result);
	}
	
	public function testDetectStumbleuponService() {
		$result = $this->service->detectService('username.stumbleupon.com');
		$this->assertService('Stumbleupon', 'username', $result);
		
		$result = $this->service->detectService('http://username.stumbleupon.com');
		$this->assertService('Stumbleupon', 'username', $result);
	}
	
	public function testDetectTwitterService() {
		$result = $this->service->detectService('twitter.com/username');
		$this->assertService('Twitter', 'username', $result);
	}
	
	public function testDetectUpcomingService() {
		$result = $this->service->detectService('upcoming.yahoo.com/user/username');
		$this->assertService('Upcoming', 'username', $result);
	}
	
	public function testDetectViddlerService() {
		$result = $this->service->detectService('viddler.com/explore/username');
		$this->assertService('Viddler', 'username', $result);
	}
	
	public function testDetectViddyouService() {
		$result = $this->service->detectService('viddyou.com/profile.php?user=username');
		$this->assertService('Viddyou', 'username', $result);
	}
	
	public function testDetectVimeoService() {
		$result = $this->service->detectService('vimeo.com/username');
		$this->assertService('Vimeo', 'username', $result);
	}
	
	public function testDetectWeventService() {
		$result = $this->service->detectService('wevent.org/users/username');
		$this->assertService('Wevent', 'username', $result);
	}
	
	public function testDetectWordpressService() {
		$result = $this->service->detectService('username.wordpress.com');
		$this->assertService('Wordpresscom', 'username', $result);
		
		$result = $this->service->detectService('http://username.wordpress.com');
		$this->assertService('Wordpresscom', 'username', $result);
	}
	
	public function testDetectXingService() {
		$result = $this->service->detectService('xing.com/profile/username');
		$this->assertService('Xing', 'username', $result);
	}
	
	public function testDetectYimService() {
		$result = $this->service->detectService('edit.yahoo.com/config/send_webmesg?.target=username&.src=pg');
		$this->assertService('Yim', 'username', $result);
	}
	
	public function testDetectZooomrService() {
		$result = $this->service->detectService('zooomr.com/photos/username');
		$this->assertService('Zooomr', 'username', $result);
	}
			
	private function assertService($expectedServiceName, $expectedUsername, $result) {
		$this->assertEqual($expectedServiceName, $result['service']);
		$this->assertEqual($expectedUsername, $result['username']);
	}
}
?>