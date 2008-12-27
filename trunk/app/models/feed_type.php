<?php
/* SVN FILE: $Id:$ */
 
class FeedType extends AppModel {
    public $useTable = false;
    
    public $types = array();
    
    public function __construct() {
        $this->types = array(
        0 => array(
            'token' => 'noserub',
            'name'  => __('NoseRub', true)
        ),
        1 => array(
            'token' => 'photo',
            'name'  => __('Photos', true)
        ),
        2 => array(
            'token' => 'link',
            'name'  => __('Links / Bookmarks', true)
        ),
        3 => array(
            'token' => 'text',
            'name'  => __('Text / Blog', true)
        ),
        4 => array(
            'token' => 'event',
            'name'  => __('Event', true)
        ),
        5 => array(
            'token' => 'micropublish',
            'name'  => __('Micropublish', true)
        ),
        6 => array(
            'token' => 'video',
            'name'  => __('Videos', true)
        ),
        7 => array(
            'token' => 'audio',
            'name'  => __('Audio', true)
        ),
        8 => array(
            'token' => 'document',
            'name'  => __('Documents', true)
        ),
        9 => array(
            'token' => 'location',
            'name'  => __('Locations', true)
        ),
        
    );
    }
    
    /**
     * returns a list, like we need it for a select box
     */
    public function getList() {
        $result = array();
        foreach($this->types as $item) {
            $result[$item['token']] = $item['name'];
        } 
        
        return $result;
    }
}