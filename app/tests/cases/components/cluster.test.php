<?php
App::import('Component', 'Cluster');

class ClusterComponentTestCase extends CakeTestCase {
    private $component = null;
    
	public function setUp() {
		$this->component = new ClusterComponent();
	}
	
	/**
	 * simply remove the duplicates based on title
	 */
	public function testRemoveDuplicates() {
	    $data = array(
	        0 => array(
	            'Entry' => array(
	                'id'           => 1,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:03' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        ),
	        2 => array(
	            'Entry' => array(
	                'id'           => 2,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:02' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        ),
	        3 => array(
	            'Entry' => array(
	                'id'           => 3,
	                'identity_id'  => 2,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:01' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        )
	    );
	    
	    $result = $this->component->removeDuplicates($data);
	    $this->assertEqual(2, count($result));
	    $this->assertEqual(2, $result[0]['Entry']['id']);
	    $this->assertEqual(3, $result[1]['Entry']['id']);
	}

	/**
	 * don't remove entries with comments.
	 * therefore, entry id 1 should stay
	 */
	public function testRemoveDuplicatesComments() {
	    $data = array(
	        0 => array(
	            'Entry' => array(
	                'id'           => 1,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:03' 
	            ),
	            'Comment' => array(
	                0 => array(
	                    'id' => 1
	                )
	            ),
	            'FavoritedBy' => array()
	        ),
	        2 => array(
	            'Entry' => array(
	                'id'           => 2,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:02' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        ),
	        3 => array(
	            'Entry' => array(
	                'id'           => 3,
	                'identity_id'  => 2,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:01' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        )
	    );
	    
	    $result = $this->component->removeDuplicates($data);
	    $this->assertEqual(2, count($result));
	    $this->assertEqual(1, $result[0]['Entry']['id']);
	    $this->assertEqual(3, $result[1]['Entry']['id']);
	}
	
	/**
	 * don't remove entries with favorites.
	 * therefore, entry id 1 should stay
	 */
	public function testRemoveDuplicatesFavorites() {
	    $data = array(
	        0 => array(
	            'Entry' => array(
	                'id'           => 1,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:03' 
	            ),
	            'Comment' => array(),
	            'FavoritedBy' => array(
	                0 => array(
	                    'id' => 1
	                )
	            )
	        ),
	        2 => array(
	            'Entry' => array(
	                'id'           => 2,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:02' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        ),
	        3 => array(
	            'Entry' => array(
	                'id'           => 3,
	                'identity_id'  => 2,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:01' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        )
	    );
	    
	    $result = $this->component->removeDuplicates($data);
	    $this->assertEqual(2, count($result));
	    $this->assertEqual(1, $result[0]['Entry']['id']);
	    $this->assertEqual(3, $result[1]['Entry']['id']);
	}
	
	/**
	 * if both have comments/favorites, keep both
	 */
	public function testRemoveDuplicatesKeepFavComments() {
	    $data = array(
	        0 => array(
	            'Entry' => array(
	                'id'           => 1,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:03' 
	            ),
	            'Comment' => array(),
	            'FavoritedBy' => array(
	                0 => array(
	                    'id' => 1
	                )
	            )
	        ),
	        2 => array(
	            'Entry' => array(
	                'id'           => 2,
	                'identity_id'  => 1,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:02' 
	            ),
	            'Comment'     => array(
	                array(
	                    0 => array(
	                        'id' => 1
	                    )
	                )
	            ),
	            'FavoritedBy' => array()
	        ),
	        3 => array(
	            'Entry' => array(
	                'id'           => 3,
	                'identity_id'  => 2,
	                'title'        => 'test 1',
	                'published_on' => '2009-01-01 12:01:01' 
	            ),
	            'Comment'     => array(),
	            'FavoritedBy' => array()
	        )
	    );
	    
	    $result = $this->component->removeDuplicates($data);
	    $this->assertEqual(3, count($result));
	    $this->assertEqual(1, $result[0]['Entry']['id']);
	    $this->assertEqual(2, $result[1]['Entry']['id']);
	    $this->assertEqual(3, $result[2]['Entry']['id']);
	}
	
	public function tearDown() {
	    unset($this->component);
	}
}	

/*

*/