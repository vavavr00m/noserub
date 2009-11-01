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
	
	public function testMicropublishMarkupLinks() {
	    $text = 'This is a text with a link: http://noserub.com';
	    $result = $this->entry->micropublishMarkup($text);
	    
	    $this->assertEqual($result, 'This is a text with a link: <a href="http://noserub.com">http://noserub.com</a>');
	}
	
	public function testMicropublishMarkupHashtag() {
	    $text = 'This is a text with a hashtag: #NoseRub';
	    $result = $this->entry->micropublishMarkup($text);
	    
	    $this->assertEqual($result, 'This is a text with a hashtag: <a href="' . Router::url('/search/') .'?q=%23NoseRub">#NoseRub</a>');
	}
	
	public function testMicropublishMarkupHashtagMultiple() {
	    $text = 'This is a text with two hashtags: #NoseRub #cool';
	    $result = $this->entry->micropublishMarkup($text);
	    
	    $this->assertEqual($result, 'This is a text with two hashtags: <a href="' . Router::url('/search/') .'?q=%23NoseRub">#NoseRub</a> <a href="' . Router::url('/search/') .'?q=%23cool">#cool</a>');
	}
	
	public function testMicropublishMarkupHashtagUmlaut() {
	    $text = 'This is a text with an umlaut hashtag: #März';
	    $result = $this->entry->micropublishMarkup($text);
	    
	    $this->assertEqual($result, 'This is a text with an umlaut hashtag: <a href="' . Router::url('/search/') .'?q=%23März">#März</a>');
	}
}
?>