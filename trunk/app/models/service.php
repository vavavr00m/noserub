<?php
/* SVN FILE: $Id:$ */
 
class Service extends AppModel {
    var $hasMany = array('Account');
    var $belongsTo = array('ServiceType');

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
     * Used by Identity::parseNoseRubPage()
     *
     * @param  
     * @return array with feed_url, service_id and service_type_id
     * @access 
     */
    function getInfo($service_url, $username) {
        # get service
        $this->recursive = 0;
        $this->expects('Service');
        $service = $this->findByUrl($service_url);
        
        $result = array();
        if($service) {
            $service_id = $service['Service']['id'];
            $result['service_id']      = $service_id;
            $result['service_type_id'] = $service['Service']['service_type_id'];
            $result['feed_url']        = $this->getFeedUrl($service_id, $username); 
        }
        
        return $result;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getServiceTypeId($service_id) {
        $this->recursive = 0;
        $this->expects('Service');
        $service = $this->findById($service_id);
        return isset($service['Service']['service_type_id']) ? $service['Service']['service_type_id']: 0;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getFeedUrl($service_id, $username) {
        switch($service_id) {
            case 1: # flickr
                # we need to read the page first in order to access
                # the user id without need to access the API
                $content = file_get_contents('http://www.flickr.com/photos/'.$username.'/');
                if(preg_match('/photos_public.gne\?id=(.*)&amp;/i', $content, $matches)) {
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
                $content = @file_get_contents('http://twitter.com/'.$username);
                if(!$content) {
                    return false;
                }
                if(preg_match('/http:\/\/twitter\.com\/statuses\/user_timeline\/([0-9]*)\.rss/i', $content, $matches)) {
                    return 'http://twitter.com/statuses/user_timeline/'.$matches[1].'.rss';
                } else {
                    return false;
                }
                
            case 6: # Pownce
                return 'http://pownce.com/feeds/public/'.$username.'/';

            case 9: # Upcoming
                return 'http://upcoming.yahoo.com/syndicate/v2/my_events/'.$username;

			case 10: # vimeo
                return 'http://vimeo.com/'.$username.'/videos/rss/';

			case 11: # Last.fm
                return 'http://ws.audioscrobbler.com/1.0/user/'.$username.'/recenttracks.rss';

			case 12: # QYPE
                return 'http://www.qype.com/people/'.$username.'/rss';

			case 13: # Ma.gnolia
                return 'http://ma.gnolia.com/rss/full/people/'.$username.'/';

			case 14: # StumbleUpon
                return 'http://www.stumbleupon.com/syndicate.php?stumbler='.$username.'';

			case 15: # Cork'd
                return 'http://corkd.com/feed/journal/'.$username.'';

			case 16: # Dailymotion
                return 'http://www.dailymotion.com/rss/'.$username.'';
            
            case 17: # Zooomr
                return 'http://www.zooomr.com/services/feeds/public_photos/?id='.$username.'&format=rss_200';

            case 18: # Odeo
                return 'http://odeo.com/profile/'.$username.'/rss.xml';

            case 19: # iLike
                return 'http://ilike.com/user/'.$username.'/recently_played.rss';

            case 20: # wevent
                return 'http://wevent.org/users/'.$username.'/upcoming.rss';

            case 21: # ImThere
                return 'http://imthere.com/users/'.$username.'/events?format=rss';

            case 22: # Newsvine
                return 'http://'.$username.'.newsvine.com/_feeds/rss2/author';

            default: 
                return false;
        }
    }
    
    /**
     * Method description
     *
     * @param  $service_id id of the service, we're about to use
     * @param  $service_type_id of the service, we're about to use
     * @param  $feed_url the url of the feed
     * @param  $items_per_feed maximum number of items that are fetched from the feed
     * @param  $items_max_age maximum age of any item that is fetched from feed, in days
     * @return 
     * @access 
     */
    function feed2array($username, $service_id, $service_type_id, $feed_url, $items_per_feed = 5, $items_max_age = '-21 days') {
        if(!$feed_url) {
            return false;
        }
        
        # get info about service type
        $this->ServiceType->id = $service_type_id;
        $intro = $this->ServiceType->field('intro');
        $token = $this->ServiceType->field('token');
        
        vendor('simplepie/simplepie');
        $max_age = $items_max_age ? date('Y-m-d H:i:s', strtotime($items_max_age)) : null;
        $items = array();

        $feed = new SimplePie();
        $feed->set_cache_location(CACHE . 'simplepie');
        $feed->set_feed_url($feed_url);
        $feed->set_autodiscovery_level(SIMPLEPIE_LOCATOR_NONE);
        $feed->init();
        if($feed->error() || $feed->feed_url != $feed_url ) {
            return false;
        }

        for($i=0; $i < $feed->get_item_quantity($items_per_feed); $i++) {
    		$feeditem = $feed->get_item($i);
    		# create a NoseRub item out of the feed item
    		$item = array();
    		$item['datetime'] = $feeditem->get_date('Y-m-d H:i:s');
    		if($max_age && $item['datetime'] < $max_age) {
    		    # we can stop here, as we do not expect any newer items
    		    break;
    		}
    		
    		$item['title']    = $feeditem->get_title();
    		$item['url']      = $feeditem->get_link();
            $item['intro']    = $intro;
            $item['type']     = $token;
            $item['username'] = $username;
            
    		switch($service_id) {
    		    case 1: # flickr
    		        $item['content'] = $this->contentFromFlickr($feeditem);
    		        break;
    		    
    		    case 2: # del.icio.us
    		        $item['content'] = $this->contentFromDelicious($feeditem);
    		        break;
    		        
    		    case 3: # ipernity
    		        $item['content'] = $this->contentFromIpernity($feeditem);
    		        break;
    		        
    		    case 4: # 23hq.com
    		        $item['content'] = $this->contentFrom23hq($feeditem);
    		        break;
    		        
    		    case 5: # Twitter
    		        $item['content'] = $this->contentFromTwitter($feeditem);
    		        $item['title']   = $item['content'];
    		        break;
    		        
    		    case 9: # Upcoming.org
    		        $item['content'] = $feeditem->get_content();
    		        break;
    		        
    		    case 10: # vimeo
    		        $item['content'] = $this->contentFromVimeo($feeditem);
    		        break;

    		    case 11: # Last.fm
    		        $item['content'] = $this->contentFromLastfm($feeditem);
    		        break;

    		    case 12: # QYPE
    		        $item['content'] = $this->contentFromQype($feeditem);
    		        break;
    		    
    		    case 13: # Ma.gnolia
    		        $item['content'] = $this->contentFromMagnolia($feeditem);
    		        break;
    		    
    		    case 14: # StumbleUpon
    		        $item['content'] = $this->contentFromStumbleupon($feeditem);
    		        break;
    		    
    		    case 15: # Cork'd
    		        $item['content'] = $this->contentFromCorkd($feeditem);
    		        break;
     		    
    		    case 16: # Dailymotion
    		        $item['content'] = $this->contentFromDailymotion($feeditem);
    		        break;
    		        
    		    case 17: # Zooomr
    		        $item['content'] = $this->contentFromZooomr($feeditem);
    		        break;
     		        
    		    case 18: # Odeo
    		        $item['content'] = $this->contentFromOdeo($feeditem);
    		        break;
     		        
    		    case 19: # iLike
    		        $item['content'] = $this->contentFromIlike($feeditem);
    		        break;

    		    case 20: # wevent
    		        $item['content'] = $this->contentFromWevent($feeditem);
    		        break;

    		    case 21: # ImThere
    		        $item['content'] = $this->contentFromImthere($feeditem);
    		        break;

    		    case 22: # Newsvine
    		        $item['content'] = $this->contentFromNewsvine($feeditem);
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
    function contentFromIpernity($feeditem) {
        $raw_content = $feeditem->get_content();
        if(preg_match('/<img width="[0-9]+" height="[0-9]+" src="(.*)l\.jpg" /iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'t.jpg" /></a>';
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
    function contentFromFlickr($feeditem) {
        $raw_content = $feeditem->get_content();
        if(preg_match('/<a href="http:\/\/www.flickr.com\/photos\/.*<\/a>/iU', $raw_content, $matches)) {
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
        # cut off the username
        $content = $feeditem->get_content();
        return substr($content, strpos($content, ': ') + 2);
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromVimeo($feeditem) {
        return $feeditem->get_content();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromLastfm($feeditem) {
        return $feeditem->get_content();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromQype($feeditem) {
        return $feeditem->get_content();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromMagnolia($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromStumbleupon($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromCorkd($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromDailymotion($feeditem) {
        return $feeditem->get_link();
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromZooomr($feeditem) {
        $raw_content = $feeditem->get_content();
        if(preg_match('/<img src="(.*)_m\.jpg"/iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'_s.jpg" /></a>';
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
    function contentFromOdeo($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromiLike($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromWevent($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromImthere($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function contentFromNewsvine($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getAccountUrl($service_id, $username) {
        switch($service_id) {
            case 1: # flickr
                return 'http://www.flickr.com/photos/'.$username.'/';
                
            case 2: # del.icio.us
                return 'http://del.icio.us/'.$username;
                
            case 3: # ipernity
                return 'http://ipernity.com/doc/'.$username.'/home/photo';
            
            case 4: # 23hq
                return 'http://www.23hq.com/'.$username;

            case 5: # twitter.com
                return 'http://twitter.com/'.$username;
                
            case 6: # pownce
                return 'http://pownce.com/'.$username.'/';
            
            case 9: # upcoming
                return 'http://upcoming.yahoo.com/user/'.$username.'/';
                
            case 10: # vimeo
                return 'http://vimeo.com/'.$username.'/';

            case 11: # Last.fm
                return 'http://www.last.fm/user/'.$username.'/';

            case 12: # QYPE
                return 'http://www.qype.com/people/'.$username.'/';

            case 13: # Ma.gnolia
                return 'http://ma.gnolia.com/people/'.$username.'/';

            case 14: # StumbleUpon
                return 'http://'.$username.'.stumbleupon.com/';
            
            case 15: # Cork'd
                return 'http://corkd.com/people/'.$username.'/';

            case 16: # Dailymotion
                return 'http://www.dailymotion.com/'.$username.'/';

            case 17: # Zooomr  
                return 'http://www.zooomr.com/photos/'.$username.'';

            case 18: # Odeo  
                return 'http://odeo.com/profile/'.$username.'';

            case 19: # iLike  
                return 'http://ilike.com/user/'.$username.'';

            case 20: # Wevent  
                return 'http://wevent.org/users/'.$username.'';

            case 21: # ImThere  
                return 'http://imthere.com/users/'.$username.'';

            case 22: # Newsvine  
                return 'http://'.$username.'.newsvine.com/';

            default:
                return '';
        }
    }
    
    /**
     * get title, url and preview for rss-feed
     *
     * @param string $feed_url 
     * @param int $max_items maximum number of items to fetch
     * @return array
     * @access 
     */
    function getInfoFromFeed($username, $service_type_id, $feed_url, $max_items = 5) {
        # needed for autodiscovery of feed
        vendor('simplepie/simplepie');
        $feed = new SimplePie();
        $feed->set_cache_location(CACHE . 'simplepie');
        $feed->set_feed_url($feed_url);
        $feed->set_autodiscovery_level(SIMPLEPIE_LOCATOR_ALL);
        @$feed->init();
        if($feed->error()) {
            return false;
        }
        
        $data = array();
        $data['title']       = $feed->get_title();
        $data['account_url'] = $feed->get_link();
        $data['feed_url']    = $feed->feed_url;
        
        unset($feed);
        
        if(!$data['account_url']) {
            $data['account_url'] = $data['feed_url'];
        }
        $data['service_id']      = 8; # any RSS-Feed
        $data['username']        = 'RSS-Feed';
        $data['service_type_id'] = $service_type_id;
        
        $items = $this->feed2array($username, 8, $data['service_type_id'], $data['feed_url'], 5, null);
    	
    	if(!$items) {
            return false;
        }
        
        $data['items'] = $items;
        
    	return $data;
    }
    
    /**
     * get service_type_id, feed_url and preview
     *
     * @param  
     * @return 
     * @access 
     */
    function getInfoFromService($username, $service_id, $account_username) {
        $this->recursive = 0;
        $this->expects('Service');
        $service = $this->findById($service_id);
        
        $data = array();
        $data['service_id']      = $service_id;
        $data['username']        = $account_username;
        $data['service_type_id'] = $service['Service']['service_type_id'];
        $data['account_url']     = $this->getAccountUrl($service_id, $account_username);
        $data['feed_url']        = $this->getFeedUrl($service_id, $account_username);
        
        $items = $this->feed2array($username, $service_id, $data['service_type_id'], $data['feed_url'], 5, null);
        
        if(!$items) {
            return false;
        }
        
        $data['items'] = $items;
        
        return $data;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromService($account_id) {
        $this->Account->recursive = 0;
        $this->Account->expects('Account');
        $account = $this->Account->findById($account_id);
        switch($account['Account']['service_id']) {
            case 1:
                return $this->getContactsFromFlickr('http://www.flickr.com/people/' . $account['Account']['username'] . '/contacts/');
                
            case 2:
                return $this->getContactsFromDelicious('http://del.icio.us/network/' . $account['Account']['username'] . '/');
                
            case 5:
                return $this->getContactsFromTwitter('http://twitter.com/' . $account['Account']['username'] . '/');
            
            case 9:
                return $this->getContactsFromUpcoming('http://upcoming.yahoo.com/user/' . $account['Account']['username'] . '/');
                
            case 10:
                return $this->getContactsFromVimeo('http://vimeo.com/' . $account['Account']['username'] . '/contacts/');

            case 11:
                return $this->getContactsFromLastfm('http://www.last.fm/user/' . $account['Account']['username'] . '/friends/');

            case 12:
                return $this->getContactsFromQype('http://www.qype.com/people/' . $account['Account']['username'] . '/contacts/');

            case 13:
                return $this->getContactsFromMagnolia('http://ma.gnolia.com/people/' . $account['Account']['username'] . '/contacts/');

            case 14:
                return $this->getContactsFromStumbleupon('http://' . $account['Account']['username'] . '.stumbleupon.com/friends/');

            case 15:
                return $this->getContactsFromCorkd('http://corkd.com/people/' . $account['Account']['username'] . '/buddies');

            case 16:
                return $this->getContactsFromDailymotion('http://www.dailymotion.com/contacts/' . $account['Account']['username'] . '');

            case 17:
                return $this->getContactsFromZooomr('http://www.zooomr.com/people/' . $account['Account']['username'] . '/contacts/');

            case 18:
                return $this->getContactsFromOdeo('http://odeo.com/profile/' . $account['Account']['username'] . '/contacts/');

            case 19:
                return $this->getContactsFromIlike('http://ilike.com/user/' . $account['Account']['username'] . '/friends');

            case 20:
                return $this->getContactsFromWevent('http://wevent.org/users/' . $account['Account']['username'] . '');

            case 21:
                return $this->getContactsFromImThere('http://imthere.com/users/' . $account['Account']['username'] . '/friends');

            case 22:
                return $this->getContactsFromNewsvine('http://' . $account['Account']['username'] . '.newsvine.com/?more=Friends&si=');

            default:
                return array();
        }
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromFlickr($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<td class="Who">.*<h2>(.*)<\/h2>/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<a href=\"\/photos\/(.*)\/\">photos<\/a>/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match('/class="Next">Next &gt;<\/a>/iU', $content)) {
                    $page_url = $url . '?page='.$i;
                    $i++;
                    if($i>1000) {
                        # just to make sure, we don't loop forever
                        break;
                    }
                } else {
                    # no "next" button found
                    break;
                }
            } else {
                # no friends found
                break;
            }
        } while(1);
        
        return $data;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromDelicious($url) {
    	return $this->__getContactsFromUrl($url, '/<a class="uname" href="\/(.*)">.*<\/a>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromUpcoming($url) {
    	return $this->__getContactsFromUrl($url, '/<a href="\/user\/[0-9]*\/">(.*)<\/a>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromVimeo($url) {
    	return $this->__getContactsFromUrl($url, '/<span class="greyd">(.*)<\/span>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromLastfm($url) {
    	return $this->__getContactsFromUrl($url, '/<a href="\/user\/(.*)\/" title=".*" class="nickname.*">.*<\/a>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromQype($url) {
    	return $this->__getContactsFromUrl($url, '/<a href="http:\/\/www.qype.com\/people\/(.*)"><img alt="Benutzerfoto: .*" src=".*" title=".*" \/><\/a>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromMagnolia($url) {
    	return $this->__getContactsFromUrl($url, '/<a href="http:\/\/ma.gnolia.com\/people\/.*" class="fn url" rel="contact" title="Visit .*">(.*)<\/a>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromStumbleupon($url) {
    	return $this->__getContactsFromUrl($url, '/<dt><a href="http:\/\/.*.stumbleupon.com\/">(.*)<\/a><\/dt>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromCorkd($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<dd class="username"><a href=".*" rel="friend">(.*)<\/a><\/dd>/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<dd class="username"><a href=".*" rel="friend">(.*)<\/a><\/dd>/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match('/Next &#8250;&#8250;<\/a>/iU', $content)) {
                    $page_url = $url . '?page='.$i;
                    $i++;
                    if($i>1000) {
                        # just to make sure, we don't loop forever
                        break;
                    }
                } else {
                    # no "next" button found
                    break;
                }
            } else {
                # no friends found
                break;
            }
        } while(1);
        
        return $data;
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromDailymotion($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<img width="80" height="80" src=".*" alt="(.*)" \/>/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<img width="80" height="80" src=".*" alt="(.*)" \/>/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match('/next&nbsp;&raquo;<\/a>/iU', $content)) {
                    $page_url = $url . '/'.$i;
                    $i++;
                    if($i>1000) {
                        # just to make sure, we don't loop forever
                        break;
                    }
                } else {
                    # no "next" button found
                    break;
                }
            } else {
                # no friends found
                break;
            }
        } while(1);
        
        return $data;
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */

    function getContactsFromZooomr($url) {
    	return $this->__getContactsFromUrl($url, '/<h2>(.*)<\/h2>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromOdeo($url) {
    	return $this->__getContactsFromUrl($url, '/<a href="\/profile\/.*" title="(.*)\'s Profile" rel="contact" id=".*">/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromIlike($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<a style=".*" class="person "  href=".*" title="View (.*)\'s profile">/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<a style=".*" class="person "  href=".*" title="View (.*)\'s profile">/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                 if(preg_match('/src="\/images\/forward_arrow.gif" title="Go forward">/iU', $content)) {
                    $page_url = $url . '?page='.$i;
                    $i++;
                    if($i>1000) {
                        # just to make sure, we don't loop forever
                        break;
                    }
                } else {
                    # no "next" button found
                    break;
                }
            } else {
                # no friends found
                break;
            }
        } while(1);
        
        return $data;
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */

    function getContactsFromWevent($url) {
    	return $this->__getContactsFromUrl($url, '/<a href="\/users\/.*" class="fn url" rel="friend">(.*)<\/a>/iU');
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromImthere($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<span class="user">(.*)<\/span><\/a>/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<span class="user">(.*)<\/span><\/a>/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                 if(preg_match('/Next<\/a><\/li>/iU', $content)) {
                    $page_url = $url . '?page='.$i;
                    $i++;
                    if($i>1000) {
                        # just to make sure, we don't loop forever
                        break;
                    }
                } else {
                    # no "next" button found
                    break;
                }
            } else {
                # no friends found
                break;
            }
        } while(1);
        
        return $data;
    }


    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromNewsvine($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<td><a href="http:\/\/.*.newsvine.com".*>(.*)<\/a>/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<td><a href="http:\/\/.*.newsvine.com".*>(.*)<\/a>/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                 if(preg_match('/title="Next 50">NEXT 50<\/a>/iU', $content)) {
                    $page_url = $url . $i;
                    $i++;
                    if($i>1000) {
                        # just to make sure, we don't loop forever
                        break;
                    }
                } else {
                    # no "next" button found
                    break;
                }
            } else {
                # no friends found
                break;
            }
        } while(1);
        
        return $data;
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function getContactsFromTwitter($url) {
    	return $this->__getContactsFromUrl($url, '/<a href="http:\/\/twitter\.com\/(.*)" class="url" rel="contact"/i');
    }
    
    function __getContactsFromUrl($url, $pattern) {
    	$data = array();
        $content = @file_get_contents($url);
        if($content && preg_match_all($pattern, $content, $matches)) {
            foreach($matches[1] as $username) {
                if(!isset($data[$username])) {
                    $data[$username] = $username;
                }
            }
        }

        return $data;
    }
}