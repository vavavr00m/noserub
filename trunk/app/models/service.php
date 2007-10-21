<?php
/* SVN FILE: $Id:$ */
 
class Service extends AppModel {
    var $hasMany = array('Account');
    var $belongsTo = array('ServiceType');
    // TODO remove this variable as soon as all services are migrated to the new structure
    private $migratedServices = array(2, 5, 9, 11, 12, 13, 14, 17, 18, 20, 36, 37);

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
        // TODO remove this handling as soon as all services are migrated to the new structure
        if (in_array($service_id, $this->migratedServices)) {
    		$service = $this->getService($service_id);
        	return $service->getFeedUrl($username);
        }
        	
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
                
            case 3: # ipernity.com
                return 'http://www.ipernity.com/feed/'.$username.'/photocast/stream/rss.xml?key=';
                
            case 4: # 23hq.com
                return 'http://www.23hq.com/rss/'.$username;
                
            case 6: # Pownce
                return 'http://pownce.com/feeds/public/'.$username.'/';

			case 10: # vimeo
                return 'http://vimeo.com/'.$username.'/videos/rss/';

			case 15: # Cork'd
                return 'http://corkd.com/feed/journal/'.$username.'';

			case 16: # Dailymotion
                return 'http://www.dailymotion.com/rss/'.$username.'';

            case 19: # iLike
                return 'http://ilike.com/user/'.$username.'/recently_played.rss';

            case 21: # ImThere
                return 'http://imthere.com/users/'.$username.'/events?format=rss';

            case 22: # Newsvine
                return 'http://'.$username.'.newsvine.com/_feeds/rss2/author';

            case 34: # Slideshare
                return 'http://www.slideshare.net/rss/user/'.$username.'';

            case 35: # Plazes
                return 'http://plazes.com/users/'.$username.'/presences.atom';

            case 38: # Digg
                return 'http://digg.com/users/'.$username.'/history/favorites.rss';

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
            
            // TODO remove this handling as soon as all services are migrated
            if (in_array($service_id, $this->migratedServices)) {
            	$service = $this->getService($service_id);
            	$item['content'] = $service->getContent($feeditem);
            	
            	if ($service instanceof TwitterService) {
            		$item['title']   = $item['content'];
            	}
            }
            
    		switch($service_id) {
    		    case 1: # flickr
    		        $item['content'] = $this->contentFromFlickr($feeditem);
    		        break;
    		        
    		    case 3: # ipernity
    		        $item['content'] = $this->contentFromIpernity($feeditem);
    		        break;
    		        
    		    case 4: # 23hq.com
    		        $item['content'] = $this->contentFrom23hq($feeditem);
    		        break;
    		    
    		   	case 6: # Pownce
    		        $item['content'] = $this->contentFromPownce($feeditem);
    		        break;
    		        
    		    case 10: # vimeo
    		        $item['content'] = $this->contentFromVimeo($feeditem);
    		        break;
    		    
    		    case 15: # Cork'd
    		        $item['content'] = $this->contentFromCorkd($feeditem);
    		        break;
     		    
    		    case 16: # Dailymotion
    		        $item['content'] = $this->contentFromDailymotion($feeditem);
    		        break;
     		        
    		    case 19: # iLike
    		        $item['content'] = $this->contentFromIlike($feeditem);
    		        break;

    		    case 21: # ImThere
    		        $item['content'] = $this->contentFromImthere($feeditem);
    		        break;

    		    case 22: # Newsvine
    		        $item['content'] = $this->contentFromNewsvine($feeditem);
    		        break;
    		        
    		    case 34: # Slideshare
    		        $item['content'] = $this->contentFromSlideshare($feeditem);
    		        break;

    		    case 35: # Plazes
    		        $item['content'] = $this->contentFromPlazes($feeditem);
    		        break;

    		    case 38: # Digg
    		        $item['content'] = $this->contentFromDigg($feeditem);
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
    private function contentFromIpernity($feeditem) {
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
    private function contentFromFlickr($feeditem) {
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
    private function contentFrom23hq($feeditem) {
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
    private function contentFromPownce($feeditem) {
        return $feeditem->get_content();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromVimeo($feeditem) {
        return $feeditem->get_content();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromCorkd($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromDailymotion($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromiLike($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromImthere($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromNewsvine($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromSlideshare($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromPlazes($feeditem) {
        return $feeditem->get_link();
    }

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    private function contentFromDigg($feeditem) {
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
	    // TODO remove this handling as soon as all services are migrated to the new structure
        if (in_array($service_id, $this->migratedServices)) {
    		$service = $this->getService($service_id);
        	return $service->getAccountUrl($username);
        }
    	
        switch($service_id) {
            case 1: # flickr
                return 'http://www.flickr.com/photos/'.$username.'/';
                
            case 3: # ipernity
                return 'http://ipernity.com/doc/'.$username.'/home/photo';
            
            case 4: # 23hq
                return 'http://www.23hq.com/'.$username;
                
            case 6: # pownce
                return 'http://pownce.com/'.$username.'/';
                
            case 10: # vimeo
                return 'http://vimeo.com/'.$username.'/';
            
            case 15: # Cork'd
                return 'http://corkd.com/people/'.$username.'/';

            case 16: # Dailymotion
                return 'http://www.dailymotion.com/'.$username.'/';

            case 19: # iLike  
                return 'http://ilike.com/user/'.$username.'';

            case 21: # ImThere  
                return 'http://imthere.com/users/'.$username.'';

            case 22: # Newsvine  
                return 'http://'.$username.'.newsvine.com/';
                
            case 23: # Jabber
            case 24: # Gtalk  
                return 'xmpp:'.$username;

            case 25: # ICQ
                return 'http://www.icq.com/'.$username;
                
            case 26: # YIM
                return 'http://edit.yahoo.com/config/send_webmesg?.target='.$username.'&.src=pg';
                
            case 27: # AIM
                return 'aim:goIM?screenname='.$username;
                
            case 28: # Skype
                return 'skype:'.$username;
                
            case 29: # MSN
                return 'msnim:'.$username;
                
            case 30: # Facebook
                return 'http://www.facebook.com/profile.php?id='.$username;
                
            case 32: # LinkedIn
                return 'http://www.linkedin.com/in/'.$username;
                
            case 33: # Xing
                return 'https://www.xing.com/profile/'.$username;
                
            case 34: #Slideshare
            	return 'http://www.slideshare.net/'.$username;

            case 35: #Plazes
            	return 'http://plazes.com/users/'.$username;

            case 38: #Digg
            	return 'http://digg.com/users/'.$username;

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
        
        $data['account_url'] = $this->getAccountUrl($service_id, $account_username);
        
        if($service['Service']['has_feed'] == 1) {
            $data['feed_url'] = $this->getFeedUrl($service_id, $account_username);
            $items            = $this->feed2array($username, $service_id, $data['service_type_id'], $data['feed_url'], 5, null);
        
            if(!$items) {
                return false;
            }
        } else {
            $data['feed_url'] = '';
            $items = array();
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
        
	    // TODO remove this handling as soon as all services are migrated to the new structure
        if (in_array($account['Account']['service_id'], $this->migratedServices)) {
    		$service = $this->getService($account['Account']['service_id']);
        	return $service->getContacts($account['Account']['username']);
        }
        
        
        switch($account['Account']['service_id']) {
            case 1:
                return $this->getContactsFromFlickr('http://www.flickr.com/people/' . $account['Account']['username'] . '/contacts/');

            case 3:
                return $this->getContactsFromIpernity('http://ipernity.com/user/' . $account['Account']['username'] . '/network');
                
            case 6:
                return $this->getContactsFromPownce('http://pownce.com/' . $account['Account']['username'] . '/friends/');
                
            case 10:
                return $this->getContactsFromVimeo('http://vimeo.com/' . $account['Account']['username'] . '/contacts/');

            case 15:
                return $this->getContactsFromCorkd('http://corkd.com/people/' . $account['Account']['username'] . '/buddies');

            case 16:
                return $this->getContactsFromDailymotion('http://www.dailymotion.com/contacts/' . $account['Account']['username'] . '');

            case 19:
                return $this->getContactsFromIlike('http://ilike.com/user/' . $account['Account']['username'] . '/friends');

            case 21:
                return $this->getContactsFromImThere('http://imthere.com/users/' . $account['Account']['username'] . '/friends');

            case 22:
                return $this->getContactsFromNewsvine('http://' . $account['Account']['username'] . '.newsvine.com/?more=Friends&si=');

            case 34:
                return $this->getContactsFromSlideshare('http://www.slideshare.net/' . $account['Account']['username'] . '/contacts');

            case 35:
                return $this->getContactsFromPlazes('http://plazes.com/users/' . $account['Account']['username'] . ';contacts');

            case 38:
                return $this->getContactsFromDigg('http://digg.com/users/' . $account['Account']['username'] . '/friends/view');

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
    private function getContactsFromFlickr($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/view his <a href="\/people\/(.*)\/">profile<\/a>/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/view his <a href="\/people\/(.*)\/">profile<\/a>/iU', $content, $usernames);
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
    private function getContactsFromIpernity($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<a href="\/user\/(.*)">Profile<\/a>/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<a href="\/user\/(.*)">Profile<\/a>/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match('/>next &rarr;<\/a>/iU', $content)) {
                    $page_url = $url . '|R58%3Bord%3D3%3Boff%3D0?r[off]='.$i;
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
    private function getContactsFromPownce($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<div class="user-name">username: (.*)<\/div>/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<div class="user-name">username: (.*)<\/div>/simU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match('/Next Page &#187;<\/a>/iU', $content)) {
                    $page_url = $url . 'page/'.$i;
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
    private function getContactsFromVimeo($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<div id="contact_(.*)">/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<div id="contact_(.*)">/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match('/<img src="\/assets\/images\/paginator_right.gif" alt="next" \/><\/a>/iU', $content)) {
                    $page_url = $url . 'sort:date/page:'.$i;
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
    private function getContactsFromCorkd($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<dd class="username"><a href="\/people\/(.*)" rel="friend">.*<\/a><\/dd>/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<dd class="username"><a href="\/people\/(.*)" rel="friend">.*<\/a><\/dd>/iU', $content, $usernames);
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
    private function getContactsFromDailymotion($url) {
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
    private function getContactsFromIlike($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<a style=".*" class="person "  href="\/user\/(.*)" title="View .*\'s profile">/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<a style=".*" class="person "  href="\/user\/(.*)" title="View .*\'s profile">/iU', $content, $usernames);
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
    private function getContactsFromImthere($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<h1 class="name"><a href="http:\/\/imthere.com\/users\/(.*)" class="friend">.*<\/a><\/h1>/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<h1 class="name"><a href="http:\/\/imthere.com\/users\/(.*)" class="friend">.*<\/a><\/h1>/iU', $content, $usernames);
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
    private function getContactsFromNewsvine($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<td><a href="http:\/\/(.*).newsvine.com".*>.*<\/a>/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<td><a href="http:\/\/(.*).newsvine.com".*>.*<\/a>/iU', $content, $usernames);
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
    private function getContactsFromSlideshare($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<a href="\/(.*)" style="" title="" class="blue_link_normal" id="">.*<\/a>/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<a href="\/(.*)" style="" title="" class="blue_link_normal" id="">.*<\/a>/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                 if(preg_match('/class="text_float_left">Next<\/a>/iU', $content)) {
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
    private function getContactsFromPlazes($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<em class="fn nickname">.*<a href="\/users\/.*" rel="vcard">\n(.*)<\/a>/simU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<em class="fn nickname">.*<a href="\/users\/.*" rel="vcard">\n(.*)<\/a>/simU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                 if(preg_match('/next<\/a><\/strong><\/p>/iU', $content)) {
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
    private function getContactsFromDigg($url) {
        $data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all('/<a class="fn" href="\/users\/(.*)">/iU', $content, $matches)) {
                # also find the usernames
                preg_match_all('/<a class="fn" href="\/users\/(.*)">/iU', $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match('/Next &#187;<\/a>/iU', $content)) {
                    $page_url = $url . '/page'.$i;
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
     * Factory method to create services
     */
    private function getService($service_id) {
    	switch ($service_id) {
    		case 2:
    			return new DeliciousService();
    		case 5: 
    			return new TwitterService();
    		case 9:
    			return new UpcomingService();
    		case 11:
    			return new LastfmService();
    		case 12:
    			return new QypeService();
    		case 13:
    			return new MagnoliaService();
    		case 14:
    			return new StumbleuponService();
    		case 17:
    			return new ZooomrService();
    		case 18:
    			return new OdeoService();
    		case 20:
    			return new WeventService();
    		case 36:
    			return new ScribdService();
    		case 37:
    			return new MoodmillService();
    	}
    }
}

class ContactExtractor {
	// TODO a better name for this function?
	static function getContactsFromUrl($url, $pattern) {
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

interface IService {
	function getAccountUrl($username);
	function getContacts($username);
	function getContent($feeditem);
	function getFeedUrl($username);
}

class DeliciousService implements IService {
	
	function getAccountUrl($username) {
		return 'http://del.icio.us/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://del.icio.us/network/' . $username . '/', '/<a class="uname" href="\/(.*)">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://del.icio.us/rss/'.$username;
	}
}

class LastfmService implements IService {
	
	function getAccountUrl($username) {
		return 'http://www.last.fm/user/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://www.last.fm/user/' . $username . '/friends/', '/<a href="\/user\/(.*)\/" title=".*" class="nickname.*">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://ws.audioscrobbler.com/1.0/user/'.$username.'/recenttracks.rss';
	}
}

class MagnoliaService implements IService {
	
	function getAccountUrl($username) {
		return 'http://ma.gnolia.com/people/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://ma.gnolia.com/people/' . $username . '/contacts/', '/<a href="http:\/\/ma.gnolia.com\/people\/(.*)" class="fn url" rel="contact" title="Visit .*">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://ma.gnolia.com/rss/full/people/'.$username.'/';
	}
}

class MoodmillService implements IService {
	
	function getAccountUrl($username) {
		return 'http://www.moodmill.com/citizen/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://www.moodmill.com/citizen/' . $username, '/<div class="who">.*<a href="http:\/\/www.moodmill.com\/citizen\/(.*)\/">.*<\/a>.*<\/div>/simU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.moodmill.com/rss/'.$username.'/';
	}
}

class OdeoService implements IService {
	
	function getAccountUrl($username) {
		return 'http://odeo.com/profile/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://odeo.com/profile/' . $username . '/contacts/', '/<a href="\/profile\/(.*)" title=".*\'s Profile" rel="contact" id=".*">/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://odeo.com/profile/'.$username.'/rss.xml';
	}
}

class QypeService implements IService {
	
	function getAccountUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://www.qype.com/people/' . $username . '/contacts/', '/<a href="http:\/\/www.qype.com\/people\/(.*)"><img alt="Benutzerfoto: .*" src=".*" title=".*" \/><\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/rss';
	}
}

class ScribdService implements IService {
	
	function getAccountUrl($username) {
		return 'http://www.scribd.com/people/view/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://www.scribd.com/people/friends/' . $username, '/<div style="font-size:16px"><a href="\/people\/view\/(.*)">.*<\/a>.*<\/div>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.scribd.com/feeds/user_rss/'.$username;
	}
}

class StumbleuponService implements IService {
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.stumbleupon.com/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://' . $username . '.stumbleupon.com/friends/', '/<dt><a href="http:\/\/(.*).stumbleupon.com\/">.*<\/a><\/dt>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.stumbleupon.com/syndicate.php?stumbler='.$username;
	}
}

class TwitterService implements IService {
	
	function getAccountUrl($username) {
		return 'http://twitter.com/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://twitter.com/' . $username . '/', '/<a href="http:\/\/twitter\.com\/(.*)" class="url" rel="contact"/i');
	}
	
	function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_content();
        return substr($content, strpos($content, ': ') + 2);
	}
	
	function getFeedUrl($username) {
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
	}
}

class UpcomingService implements IService {
	
	function getAccountUrl($username) {
		return 'http://upcoming.yahoo.com/user/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://upcoming.yahoo.com/user/' . $username . '/', '/<a href="\/user\/[0-9]*\/">(.*)<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://upcoming.yahoo.com/syndicate/v2/my_events/'.$username;
	}
}

class WeventService implements IService {
	
	function getAccountUrl($username) {
		return 'http://wevent.org/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://wevent.org/users/' . $username, '/<a href="\/users\/(.*)" class="fn url" rel="friend">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://wevent.org/users/'.$username.'/upcoming.rss';
	}
}

class ZooomrService implements IService {
	
	function getAccountUrl($username) {
		return 'http://www.zooomr.com/photos/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromUrl('http://www.zooomr.com/people/' . $username . '/contacts/', '/View their <a href="\/people\/(.*)\/">profile<\/a><\/p>/iU');
	}
	
	function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        if(preg_match('/<img src="(.*)_m\.jpg"/iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'_s.jpg" /></a>';
        }
        return '';
	}
	
	function getFeedUrl($username) {
		return 'http://www.zooomr.com/services/feeds/public_photos/?id='.$username.'&format=rss_200';
	}
}