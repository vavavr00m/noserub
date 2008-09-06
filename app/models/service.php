<?php
/* SVN FILE: $Id:$ */
 
class Service extends AppModel {
    public $hasMany = array('Account');
    public $belongsTo = array('ServiceType');

    /**
     * returns all accounts with is_contact=1
     *
     * @return array
     */
    public function getContactAccounts() {
        $this->contain();
        return $this->findAllByIsContact(1);
    }
    
    public function detectService($url) {
    	$url = trim($url);
    	if($url == '') {
    		return false;
    	}
    	
    	$url = $this->removeHttpProtocol($url);
    	$services = $this->getAllServices();

    	foreach($services as $service) {
    		$username = $service->detectService($url);
    		
    		if($username) {
    			return array('service_id' => $service->getServiceId(), 'username' => $username);
    		}
    	}
    	
    	return false;
    }
    
    /**
     * Used by Identity::parseNoseRubPage()
     *
     * @param  
     * @return array with feed_url, service_id and service_type_id
     * @access 
     */
    public function getInfo($service_url, $username) {
        # get service
        $this->contain();
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
    
    public function getServiceTypeId($service_id) {
        $this->contain();
        $service = $this->findById($service_id);
        return isset($service['Service']['service_type_id']) ? $service['Service']['service_type_id']: 0;
    }
    
    public function getFeedUrl($service_id, $username) {
    	$service = $this->getService($service_id);
		
    	if($service) {
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
    public function feed2array($username, $service_id, $service_type_id, $feed_url, $items_per_feed = 5, $items_max_age = '-21 days') {
        if(!$feed_url || strpos($feed_url, '//friendfeed.com/') > 0) {
            return false;
        }
        
        # get info about service type
        $this->ServiceType->id = $service_type_id;
        $intro = $this->ServiceType->field('intro');
        $token = $this->ServiceType->field('token');
		$service_type_filter = ServiceTypeFilterFactory::getFilter($service_type_id);
        
        $max_age = $items_max_age ? date('Y-m-d H:i:s', strtotime($items_max_age)) : null;
        $items = array();

		$feed = $this->createSimplePie($feed_url);
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
			$item = $service_type_filter->filter($item);
    		$items[] = $item; 
    	}

        unset($feed);
        
        return $items;
    }

    public function getAccountUrl($service_id, $username) {
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
    public function getInfoFromFeed($username, $service_type_id, $feed_url, $max_items = 5) {
        # needed for autodiscovery of feed
    	$feed = $this->createSimplePie($feed_url, true);
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
    public function getInfoFromService($username, $service_id, $account_username) {
        $this->contain();
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
    
    public function getContactsFromService($account_id) {
        $this->Account->contain();
        $account = $this->Account->findById($account_id);
        
    	$service = $this->getService($account['Account']['service_id']);

    	if($service) {
    		return $service->getContacts($account['Account']['username']);
        }
        
        return array();
    }

    private function createService($service_id, $service_name) {
    	$class_name = $service_name . 'Service';
    	
    	if(!class_exists($class_name)) {
    		require(MODELS.'services'.DS.strtolower($service_name).'.php');
    	}
    	
    	return new $class_name($service_id);
    }
    
    private function createSimplePie($feed_url, $autodiscovery = false) {
    	App::import('Vendor', 'simplepie'.DS.'simplepie');
        $feed = new SimplePie();
        $feed->set_feed_url($feed_url);
        $feed->enable_cache(false);
        
        $autodiscovery_level = SIMPLEPIE_LOCATOR_NONE;
        if($autodiscovery) {
        	$autodiscovery_level = SIMPLEPIE_LOCATOR_ALL;
        }
        
        $feed->set_autodiscovery_level($autodiscovery_level);
        
        return $feed;
    }
    
    private function getAllServices() {
    	$serviceObjects = array();

    	$this->recursive = 0;
    	$services = $this->find('all', array('conditions' => array('service_type_id >' => 0), 'fields' => array('id', 'internal_name')));
    	
    	foreach($services as $service) {
    		$serviceObjects[] = $this->createService($service['Service']['id'], $service['Service']['internal_name']);
    	}
    	
    	return $serviceObjects;
    }
    
    /**
     * Factory method to create services
     */
    private function getService($service_id) {
    	// RSS feeds are not handled by this method, so we simply return false
    	if($service_id == 8) {
    		return false;
    	}
    	
    	$this->recursive = 0;
    	$service = $this->find(array('Service.id' => $service_id), array('Service.internal_name'));
    	
    	if(!$service) {
    		return false;
    	}
    	
    	return $this->createService($service_id, $service['Service']['internal_name']);
    }
    
    private function removeHttpProtocol($url) {
    	$url = str_ireplace('http://', '', $url);
    	$url = str_ireplace('https://', '', $url);
    	
    	return $url;
    }
}

class ContactExtractor {
	public static function getContactsFromSinglePage($url, $pattern) {
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
	public static function getContactsFromMultiplePages($url, $pattern, $secondPattern, $urlPart) {
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
	
	public function __construct($service_id) {
		$this->service_id = $service_id;
	}

	/**
	 * Implementations of this function have to return boolean false if 
	 * the service couldn't be detected, or a string with the username.
	 */
	public abstract function detectService($url);
	
	protected function extractUsername($url, $patterns) {
		foreach ($patterns as $pattern) {
			preg_match($pattern, $url, $matches);
		
			if (!empty($matches)) {
			    # there was a case, where a trailing "/" was at the
			    # flickr username. And we forbid to save accounts
			    # with /, so we can delete them here, when at the end
			    return trim(urldecode($matches[1]), '/ ');
			}
		}
		
		return false;
	}
	
	public function getAccountUrl($username) {
		return '';
	}
	
	public function getContacts($username) {
		return array();
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return false;
	}
	
	public final function getServiceId() {
		return $this->service_id; 
	}
}

class ServiceTypeFilterFactory {
	public static function getFilter($service_type_id) {
		if ($service_type_id == 1) {
			return new PhotoFilter();
		}
		
		return new DummyFilter();
	}
}

interface IServiceTypeFilter {
	public function filter($item);
}

class DummyFilter implements IServiceTypeFilter {
	public function filter($item) {
		return $item;
	}
}

class PhotoFilter implements IServiceTypeFilter {
	public function __construct() {
		App::import('Vendor', 'htmlpurifier', array('file' => 'htmlpurifier'.DS.'HTMLPurifier.auto.php'));		
	}
	
	public function filter($item) {
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML', 'Allowed', 'img[src|alt]');

		$purifier = new HTMLPurifier($config);
		$clean_html = $purifier->purify($item['content']);
		$clean_html = str_replace('<img src=', '<img width="75" height="75" src=', $clean_html);
		$img_src = substr($clean_html, stripos($clean_html, '<img '));
		$img_src = substr($img_src, 0, stripos($img_src, '>'));
		
		$item['content'] = '<a href="' . $item['url'] . '">' . $img_src . '</a>';
		
		return $item;
	}
}