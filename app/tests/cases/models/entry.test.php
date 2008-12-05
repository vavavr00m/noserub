<?php
App::import('Model', 'Entry');

class EntryTest extends CakeTestCase {
	private $service = null;
	
	public function setUp() {
		$this->entry = new Entry();
	}
	
	public function testShortenLongUrl() {
        $text = 'This is a text with a long url: http://noserub/app/webroot/test.php?show=cases&app=true';
        $result = $this->entry->shorten($text);
        
        $this->assertEqual($result, 'This is a text with a long url: http://li.ttle.de/g');
	}
	
	public function testShortenShortUrl() {
        $text = 'This is a text with a short url: http://ff.im/1';
        $result = $this->entry->shorten($text);
        
        $this->assertEqual($result, $text);
	}
}
?>