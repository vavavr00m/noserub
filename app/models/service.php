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
                
            case 6: # Pownce
                return 'http://pownce.com/feeds/public/'.$username.'/';
                
            default: 
                return false;
        }
    }
    
    /**
     * Method description
     *
     * @param  $service_id id of te service, we're about to use
     * @param  $feedurl the url of the feed
     * @param  $items_per_feed maximum number of items that are fetched from the feed
     * @param  $items_max_age maximum age of any item that is fetched from feed, in days
     * @return 
     * @access 
     */
    function feed2array($service_id, $feedurl, $items_per_feed = 5, $items_max_age = '-21 days') {
        vendor('simplepie/simplepie');
        $max_age = date('Y-m-d H:i:s', strtotime($items_max_age));
        $items = array();

        $feed = new SimplePie();
        $feed->set_cache_location(CACHE . 'simplepie');
        $feed->set_feed_url($feedurl);
        $feed->init();
        for($i=0; $i < $feed->get_item_quantity($items_per_feed); $i++) {
    		$feeditem = $feed->get_item($i);
    		# create a NoseRub item out of the feed item
    		$item = array();
    		$item['datetime'] = $feeditem->get_date('Y-m-d H:i:s');
    		if($item['datetime'] < $max_age) {
    		    # we can stop here, as we do not expect any newer items
    		    break;
    		}
    		
    		$item['link'] = $feeditem->get_link();
    		
    		switch($service_id) {
    		    case 1: # flickr
    		        $item['content'] = $this->contentFromFlickr($feeditem);
    		        break;
    		    
    		    case 2: # del.icio.us
    		        $item['content'] = $this->contentFromDelicious($feeditem);
    		        break;
    		        
    		    case 4: # 23hq.com
    		        $item['content'] = $this->contentFrom23hq($feeditem);
    		        break;
    		        
    		    case 5: # Twitter
    		        $item['content'] = $this->contentFromTwitter($feeditem);
    		        break;
    		        
    		    default:
    		        $item['content'] = $feeditem->get_content();
    		}
    		$items[] = $item; 
    	}

        unset($feed);
        
        return $items;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromFlickr($feeditem) {
        $raw_content = $feeditem->get_content();
        if(preg_match('/<a href="http:\/\/www.flickr.com\/photos.*<\/a>/i', $raw_content, $matches)) {
            $content = str_replace('_m.jpg', '_s.jpg', $matches[0]);
            $content = preg_replace('/width="[0-9]+".+height="[0-9]+"/i', '', $content);
            return $content;
        }
        return '';
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromDelicious($feeditem) {
        return $feeditem->get_link();
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFrom23hq($feeditem) {
        $raw_content = $feeditem->get_content();
        #<a href="http://www.23hq.com/DonDahlmann/photo/2204674
        if(preg_match('/<a href="http:\/\/www.23hq.com\/.*\/photo\/.*<\/a>/iU', $raw_content, $matches)) {
            $content = str_replace('standard', 'quad100', $matches[0]);
            $content = preg_replace('/width="[0-9]+".+height="[0-9]+"/i', '', $content);
            return $content;
        }
        return '';
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromTwitter($feeditem) {
        return $feeditem->get_content();
    }
}