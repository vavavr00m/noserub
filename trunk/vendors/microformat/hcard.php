<?php

require_once('hkit.class.php');
require_once('base.php');

class hcard extends microformat_base {
    private $hKit;
    
    public function __construct() {
        $this->hKit = new hKit;
        $this->hKit->tidy_mode = 'none';
    }
    
    public function getByUrl($url) {
        $result = false;
        $content = WebExtractor::fetchUrl($url);
        
        if($content) {
    	    $result	= @$this->hKit->getByString('hcard', $content);
    	    if(!$result) {
    	        $item = array();
    	        $fns = array();
    	        $photos = array();
                $vcards = $this->getValues($content, 'class', 'vcard');
                foreach($vcards as $vcard) {
                    $fn = $this->getValues($vcard, 'class', 'fn');
                    $fns = array_merge($fns, $fn);
                    
                    $photo = $this->getValues($vcard, 'class', 'photo');
                    $photos = array_merge($photos, $photo);
                }
                foreach($fns as $fn) {
                    if($fn->nodeValue) {
                        $item['fn'] = $fn->nodeValue;
                        $names = split(' ', $item['fn']);
    	                if(count($names) > 1) {
    	                    $item['n'] = array(
    	                        'given-name'  => array_shift($names),
    	                        'family-name' => join(' ', $names)
    	                    );
	                    }
                    }
                }

                foreach($photos as $photo) {
                    $src = '';
                    if($photo->nodeName == 'img') {
                        $src = $photo->getAttribute('src');
                    }
                    if($src) {
                        $item['photo'] = $src;
                    }
                }
                
                if($item) {
                    $result = array(0 => $item);
                }
            }
        }
        
	    return $result;
    }
}