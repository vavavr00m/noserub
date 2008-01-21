<?php
/* SVN FILE: $Id:$ */
 
class Service extends AppModel {
    var $hasMany = array('Account');
    var $belongsTo = array('ServiceType');

    function detectService($url) {
    	$url = trim($url);
    	if ($url == '') {
    		return false;
    	}
    	
    	$url = $this->removeHttpProtocol($url);
    	$services = $this->getAllServices();

    	foreach ($services as $service) {
    		$username = $service->detectService($url);
    		
    		if ($username) {
    			return array('service_id' => $service->getServiceId(), 'username' => $username);
    		}
    	}
    	
    	return false;
    }
    
    function getDomainFromString($url) {
    	if (strlen(trim($url)) < 4 || strpos($url, '.') === false) {
    		return false;
    	}

    	$domain = $this->removeHttpProtocol($url);
    	$domain = $this->removePath($domain);
    	$domain = $this->removeSubdomains($domain);
    	
    	return $domain;
    }
    
    function getServiceFromString($url) {
    	$domain = $this->getDomainFromString($url);
    	
    	if ($domain) {
    		$domain = Sanitize::paranoid($domain, array('.', '-'));
    		$service = $this->find(array('Service.url' => 'LIKE %'.$domain.'%'), 'Service.id');
    		
    		if ($service) {
    			return $service['Service']['id'];
    		} else {
    			// it is an unknown service, so we treat it as a RSS feed
    			return 8;
    		}
    	}
    	
    	return false;
    }
    
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
    	$service = $this->getService($service_id);
		
    	if ($service) {
    		return $service->getFeedUrl($username);
        }
                
        return false;
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
            
            $service = $this->getService($service_id);

            if ($service) {
            	$item['content'] = $service->getContent($feeditem);
            	
            	if ($service instanceof TwitterService) {
            		$item['title']   = $item['content'];
            	}
            } else {
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
    function getAccountUrl($service_id, $username) {
    	$service = $this->getService($service_id);

    	if ($service) {
    		return $service->getAccountUrl($username);
        }
    	
        return '';
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
        
    	$service = $this->getService($account['Account']['service_id']);

    	if ($service) {
    		return $service->getContacts($account['Account']['username']);
        }
        
        return array();
    }

    private function getAllServices() {
    	$services = array();
    	
    	for ($i = 1; $i <= 53; $i++) {
    		$service = $this->getService($i);
    		
    		if ($service) {
    			$services[] = $service;
    		}
    	}

    	return $services;
    }
    
    /**
     * Factory method to create services
     */
    private function getService($service_id) {
    	$serviceName = '';
    	
    	switch ($service_id) {
    		case 1:
    			$serviceName = 'Flickr';
    			break;
    		case 2:
    			$serviceName = 'Delicious';
    			break;
    		case 3:
    			$serviceName = 'Ipernity';
    			break;
    		case 4:
    			$serviceName = '_23hq';
    			break;
    		case 5: 
    			$serviceName = 'Twitter';
    			break;
    		case 6:
    			$serviceName = 'Pownce';
    			break;
    		case 9:
    			$serviceName = 'Upcoming';
    			break;
    		case 10:
    			$serviceName = 'Vimeo';
    			break;
    		case 11:
    			$serviceName = 'Lastfm';
    			break;
    		case 12:
    			$serviceName = 'Qype';
    			break;
    		case 13:
    			$serviceName = 'Magnolia';
    			break;
    		case 14:
    			$serviceName = 'Stumbleupon';
    			break;
    		case 15:
    			$serviceName = 'Corkd';
    			break;
    		case 16:
    			$serviceName = 'Dailymotion';
    			break;
    		case 17:
    			$serviceName = 'Zooomr';
    			break;
    		case 18:
    			$serviceName = 'Odeo';
    			break;
    		case 19:
    			$serviceName = 'Ilike';
    			break;
    		case 20:
    			$serviceName = 'Wevent';
    			break;
    		case 21:
    			$serviceName = 'Imthere';
    			break;
    		case 22:
    			$serviceName = 'Newsvine';
    			break;
    		case 23:
    			$serviceName = 'Jabber';
    			break;
    		case 24:
    			$serviceName = 'Gtalk';
    			break;
    		case 25:
    			$serviceName = 'Icq';
    			break;
    		case 26:
    			$serviceName = 'Yim';
    			break;
    		case 27:
    			$serviceName = 'Aim';
    			break;
    		case 28:
    			$serviceName = 'Skype';
    			break;
    		case 29:
    			$serviceName = 'Msn';
    			break;
    		case 30:
    			$serviceName = 'Facebook';
    			break;
    		case 31:
    			$serviceName = 'Secondlife';
    			break;
    		case 32:
    			$serviceName = 'Linkedin';
    			break;
    		case 33:
    			$serviceName = 'Xing';
    			break;
    		case 34:
    			$serviceName = 'Slideshare';
    			break;
    		case 35:
    			$serviceName = 'Plazes';
    			break;
    		case 36:
    			$serviceName = 'Scribd';
    			break;
    		case 37:
    			$serviceName = 'Moodmill';
    			break;
    		case 38:
    			$serviceName = 'Digg';
    			break;
    		case 39:
    			$serviceName = 'Misterwong';
    			break;
    		case 40:
    			$serviceName = 'Folkd';
    			break;
    		case 41:
    			$serviceName = 'Reddit';
    			break;
    		case 42:
    			$serviceName = 'Faves';
    			break;
    		case 43:
    			$serviceName = 'Simpy';
    			break;
    		case 44:
    			$serviceName = 'Deviantart';
    			break;
    		case 45:
    			$serviceName = 'Viddler';
    			break;
    		case 46:
    			$serviceName = 'Viddyou';
    			break;
    		case 47:
    			$serviceName = 'Gadugadu';
    			break;
    		case 48:
    			$serviceName = 'Dopplr';
    			break;
    		case 49:
    			$serviceName = 'Orkut';
    			break;
    		case 50:
    			$serviceName = 'Kulando';
    			break;
    		case 51:
    			$serviceName = 'Wordpresscom';
    			break;
    		case 52:
    			$serviceName = 'Bloggerde';
    			break;
    		case 53:
    			$serviceName = 'Livejournal';
    			break;
    		default:
    			return false;
    	}
    	
    	$className = $serviceName . 'Service';
    	
    	if (!class_exists($className)) {
    		require(MODELS.'services'.DS.strtolower($serviceName).'.php');
    	}
    	
    	return new $className($service_id);
    }
    
    private function removePath($url) {
    	$positionOfSlash = strpos($url, '/');
    	if ($positionOfSlash) {
    		$url = substr($url, 0, $positionOfSlash);    	
    	}
    	
    	return $url;
    }
    
    private function removeHttpProtocol($url) {
    	$url = str_ireplace('http://', '', $url);
    	$url = str_ireplace('https://', '', $url);
    	
    	return $url;
    }
    
    private function removeSubdomains($url) {
    	$dotCount = substr_count($url, '.');
    	if ($dotCount > 1) {
    		$revertedUrl = strrev($url);
    		$url = strrev(substr($revertedUrl, 0, strpos($revertedUrl, '.', 4)));
    	}
    	
    	return $url;
    }
}

class ContactExtractor {

	static function getContactsFromSinglePage($url, $pattern) {
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
	
	// TODO better names for the parameters
	static function getContactsFromMultiplePages($url, $pattern, $secondPattern, $urlPart) {
		$data = array();
        $i = 2;
        $page_url = $url;
        do {
            $content = @file_get_contents($page_url);
            if($content && preg_match_all($pattern, $content, $matches)) {
                # also find the usernames
                preg_match_all($pattern, $content, $usernames);
                foreach($usernames[1] as $idx => $username) {
                    if(!isset($data[$username])) {
                        $data[$username] = $matches[1][$idx];
                    }
                }
                if(preg_match($secondPattern, $content)) {
                    $page_url = $url . $urlPart . $i;
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
}

/**
 * Base class for all services.
 */
abstract class AbstractService {
	private $service_id;
	
	function __construct($service_id) {
		$this->service_id = $service_id;
	}

	/**
	 * Implementations of this function have to return boolean false if 
	 * the service couldn't be detected, or a string with the username.
	 */
	abstract function detectService($url);
	
	protected function extractUsername($url, $patterns) {
		foreach ($patterns as $pattern) {
			preg_match($pattern, $url, $matches);
		
			if (!empty($matches)) {
				return $matches[1];
			}
		}
		
		return false;
	}
	
	function getAccountUrl($username) {
		return '';
	}
	
	function getContacts($username) {
		return array();
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return false;
	}
	
	final function getServiceId() {
		return $this->service_id; 
	}
}

class OrkutService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#orkut.com/Profile.aspx\?uid=(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.orkut.com/Profile.aspx?uid='.$username;
	}
}

class PlazesService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#plazes.com/users/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://plazes.com/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://plazes.com/users/' . $username . ';contacts', '/<em class="fn nickname">.*<a href="\/users\/.*" rel="vcard">\n(.*)\s{6}<\/a>/simU', '/next<\/a><\/strong><\/p>/iU', '?page=');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://plazes.com/users/'.$username.'/presences.atom';
	}
}

class PownceService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#pownce.com/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://pownce.com/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://pownce.com/' . $username . '/friends/', '/<div class="user-name">username: (.*)<\/div>/simU', '/Next Page &#187;<\/a>/iU', 'page/');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://pownce.com/feeds/public/'.$username.'/';
	}
}

class QypeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#qype.com/people/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.qype.com/people/' . $username . '/contacts/', '/<a href="http:\/\/www.qype.com\/people\/(.*)"><img alt="Benutzerfoto: .*" src=".*" title=".*" \/><\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/rss';
	}
}

class RedditService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#reddit.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://reddit.com/user/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://reddit.com/user/' . $username . '/contacts/', '/<a href="\/profile\/(.*)" title=".*\'s Profile" rel="contact" id=".*">/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://reddit.com/user/'.$username.'/.rss';
	}
}

class ScribdService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#scribd.com/people/view/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.scribd.com/people/view/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.scribd.com/people/friends/' . $username, '/<div style="font-size:16px"><a href="\/people\/view\/(.*)">.*<\/a>.*<\/div>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.scribd.com/feeds/user_rss/'.$username;
	}
}

class SecondlifeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^#(.+)/'));
	}
	
	function getAccountUrl($username) {
		return '#'.$username;
	}	
}

class SimpyService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#simpy.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.simpy.com/user/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://reddit.com/user/' . $username . '/contacts/', '/<a href="\/profile\/(.*)" title=".*\'s Profile" rel="contact" id=".*">/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.simpy.com/rss/user/'.$username.'/links/';
	}
}

class SkypeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^skype:(.+)/'));
	}
	
	function getAccountUrl($username) {
		return 'skype:'.$username;
	}
}

class SlideshareService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#slideshare.net/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.slideshare.net/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://www.slideshare.net/' . $username . '/contacts', '/<a href="\/(.*)" style="" title="" class="blue_link_normal" id="">.*<\/a>/iU', '/class="text_float_left">Next<\/a>/iU', '/');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.slideshare.net/rss/user/'.$username;
	}
}

class StumbleuponService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).stumbleupon.com#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.stumbleupon.com/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://' . $username . '.stumbleupon.com/friends/', '/<dt><a href="http:\/\/(.*).stumbleupon.com\/">.*<\/a><\/dt>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.stumbleupon.com/syndicate.php?stumbler='.$username;
	}
}

class TwitterService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#twitter.com/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://twitter.com/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://twitter.com/' . $username . '/', '/<a href="http:\/\/twitter\.com\/(.*)" class="url" rel="contact"/i');
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

class UpcomingService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#upcoming.yahoo.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://upcoming.yahoo.com/user/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://upcoming.yahoo.com/user/' . $username . '/', '/<a href="\/user\/[0-9]*\/">(.*)<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://upcoming.yahoo.com/syndicate/v2/my_events/'.$username;
	}
}

class ViddlerService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#viddler.com/explore/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.viddler.com/explore/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.viddler.com/explore/' . $username . '/friends/', '/<p><strong><a.*href="\/explore\/.*\/".*>(.*)<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://www.viddler.com/explore/'.$username.'/videos/feed/';
	}
}

class ViddyouService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#viddyou.com/profile.php\?user=(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://viddyou.com/profile.php?user='.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://viddyou.com/profile.php?user=' . $username . '/friends/', '/next>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://www.viddyou.com/feed/user/'.$username.'/feed.rss';
	}
}

class VimeoService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#vimeo.com/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://vimeo.com/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://vimeo.com/' . $username . '/contacts/', '/<div id="contact_(.*)">/iU', '/<img src="\/assets\/images\/paginator_right.gif" alt="next" \/><\/a>/iU', 'sort:date/page:');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://vimeo.com/'.$username.'/videos/rss/';
	}
}

class WeventService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#wevent.org/users/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://wevent.org/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://wevent.org/users/' . $username, '/<a href="\/users\/(.*)" class="fn url" rel="friend">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://wevent.org/users/'.$username.'/upcoming.rss';
	}
}

class WordpresscomService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).wordpress.com#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.wordpress.com';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://' . $username . 'wordpress.com', '/<a href="http:\/\/(.*).wordpress.com" rel=".*">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://'.$username.'.wordpress.com/feed/';
	}
}

class XingService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#xing.com/profile/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'https://www.xing.com/profile/'.$username;
	}
}

class YimService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#edit.yahoo.com/config/send_webmesg\?.target=(.+)&.src=pg#'));
	}
	
	function getAccountUrl($username) {
		return 'http://edit.yahoo.com/config/send_webmesg?.target='.$username.'&.src=pg';
	}
}

class ZooomrService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#zooomr.com/photos/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.zooomr.com/photos/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.zooomr.com/people/' . $username . '/contacts/', '/View their <a href="\/people\/(.*)\/">profile<\/a><\/p>/iU');
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