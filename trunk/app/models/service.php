<?php
/* SVN FILE: $Id:$ */
 
class Service extends AppModel {
    var $hasMany = array('Account');

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getSelect($type = 'all') {
        $this->recursive = 0;
        $this->expects = array('Service');
        if($type == 'all') {
            $data = $this->findAll();
        } else {
            $data = $this->findAll(array('type' => $type));
        }
        
        $services = array();
        foreach($data as $item) {
            $services[$item['Service']['id']] = $item['Service']['name'];
        }
        
        return $services;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function username2Feed($username, $service_id) {
        switch($service_id) {
            case 1: # flickr
                # we need to read the page first in order to access
                # the user id without need to access the API
                $content = file_get_contents('http://www.flickr.com/photos/'.$username.'/');
                if(preg_match('/photos_public.gne\?id=([0-9a-z@]*)&amp;amp;/i', $content, $matches)) {
                    return 'http://api.flickr.com/services/feeds/photos_public.gne?id='.$matches[1].'&lang=en-us&format=rss_200';
                } else {
                    return false;
                }
                
            case 2: # del.icio.us
                return 'http://del.icio.us/rss/'.$username;
                
            case 3: # ipernity.com
                return 'http://www.ipernity.com/feed/'.$username.'/photocast/stream/rss.xml?key=';
                
            case 4: # 23hq.com
                return 'http://www.23hq.com/rss/'.$username;
                
            case 5: # Twitter
                # we need to reed the page first in order to
                # access the rss-feed
                $content = file_get_contents('http://twitter.com/'.$username);
                if(preg_match('/http:\/\/twitter\.com\/statuses\/user_timeline\/([0-9]*)\.rss/i', $content, $matches)) {
                    return 'http://twitter.com/statuses/user_timeline/'.$matches[1].'.rss';
                } else {
                    return false;
                }
                
            default: 
                return false;
        }
    }
}