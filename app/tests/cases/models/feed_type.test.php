<?php
App::import('Model', 'FeedType');

class FeedTypeTest extends CakeTestCase {
	public function setUp() {
		$this->FeedType = new FeedType();
	}

	public function testGetList() {
	    $expected = array(
	        'noserub'      => __('NoseRub', true),
	        'photo'        => __('Photos', true),
	        'link'         => __('Links / Bookmarks', true),
	        'text'         => __('Text / Blog', true),
	        'event'        => __('Event', true),
	        'micropublish' => __('Micropublish', true),
	        'video'        => __('Videos', true),
	        'audio'        => __('Audio', true),
	        'document'     => __('Documents', true),
	        'location'     => __('Locations', true)
	    );
	    
	    $this->assertEqual($expected, $this->FeedType->getList());
	}
}