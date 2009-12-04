<?php
/* SVN FILE: $Id:$ */
 
class Service extends AppModel {
    public $hasMany = array('Account');
    public $useTable = false;
    
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
    	
    	App::import('Vendor', 'UrlUtil');
    	$url = UrlUtil::removeHttpAndHttps($url);
    	$this->getAllServices();

        $services = Configure::read('services');
    	foreach($services['list'] as $service_name => $value) {
    	    $service = $this->createService($service_name);
    		$username = $service->detectService($url);
    		
    		if($username) {
    			return array(
    			    'service' => $service_name, 
    			    'username' => $username
    			);
    		}
    	}
    	
    	return false;
    }
    
    /**
     * Used by Identity::parseNoseRubPage()
     *
     * @return array with feed_url, service and service_type 
     */
    public function getInfo($service_url, $username) {
        App::import('Vendor', 'UrlUtil');
    	$service_url = UrlUtil::unify($service_url);
    	
        # get service
        $service = false;
        $service_name = '';
        $services = Configure::read('services.data');
        foreach($services as $key => $item) {
            // $item['url'] is normally no arary,
            // but for delicious.com and del.icio.us we
            // need them. So here we treat the field,
            // as if it is always an arary.
            $urls = $item['url'];
            if(!is_array($urls)) {
                $urls = array($item['url']);
            }
            foreach($urls as $raw_url) {
                $url = UrlUtil::unify($raw_url);
                if($url == $service_url) {
                    $service_name = $key;
                    $service = $item;
                    break;
                }
                if($service) {
                    break;
                }
            }
        }
        
        $result = array();
        if($service) {
            $result['service'] = $service_name;
            $result['service_type'] = $service['service_type'];
            $result['feed_url'] = $this->getFeedUrl($service_name, $username); 
            $result['username'] = $username;
        }
        
        return $result;
    }

    public function getServiceTypeId($service_name) {
        $service = $this->getService($service_name);
        
        return $service['service_type'];
    }
    
    public function getFeedUrl($service_name, $username) {
    	$service = $this->getService($service_name);
		
    	if($service) {
    		return $service->getFeedUrl($username);
        }
                
        return false;
    }
    
    /**
     * Method description
     *
     * @param string $username
     * @param string $service_name name of the service, we're about to use
     * @param int $service_type of the service, we're about to use
     * @param string$feed_url the url of the feed
     * @param int $items_per_feed maximum number of items that are fetched from the feed
     *
     * @return 
     */
    public function feed2array($username, $service_name, $service_type, $feed_url, $items_per_feed = 10) {
        if(!$feed_url || strpos($feed_url, '//friendfeed.com/') > 0) {
            return false;
        }
        
        # get info about service type
        $service_types = Configure::read('service_types');
        $intro = $service_types[$service_type]['intro'];
        $token = $service_types[$service_type]['token'];
        
		$service_type_filter = ServiceTypeFilterFactory::getFilter($service_type);
        
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
    		
    		$item['url']       = $feeditem->get_link();
            $item['intro']     = $intro;
            $item['type']      = $token;
            $item['username']  = $username;
            $item['latitude']  = $feeditem->get_latitude();
            $item['longitude'] = $feeditem->get_longitude();

            $service = $this->getService($service_name);

            if($service) {
            	$item['content'] = $service->getContent($feeditem);
            	if(is_array($item['content'])) {
            	    $item['content'] = base64_encode(serialize($item['content']));
            	}
            	$item['title'] = $service->getTitle($feeditem);    
            	$item['datetime'] = $service->getDatetime($feeditem);        	
            } else {
                # when no service was found, this is a simple RSS feed
            	$item['content'] = $feeditem->get_content();
            	$item['title'] = $feeditem->get_title();
            	$item['datetime'] = $feeditem->get_date();
            }
        	
			$item = $service_type_filter->filter($item);
    		$items[] = $item; 
    	}
        $feed->__destruct();
        unset($feed);
        
        return $items;
    }

    public function getAccountUrl($service_name, $username) {
    	$service = $this->getService($service_name);

    	if($service) {
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
    public function getInfoFromFeed($username, $service_type, $feed_url, $max_items = 5) {
        # needed for autodiscovery of feed
    	$feed = $this->createSimplePie($feed_url, true);
        $feed->init();
        
        $data = array();
        $data['title'] = $feed->get_title();
        $data['account_url'] = $feed->get_link();
        
        if($feed->error()) {
            $data['feed_url'] = '';
            $data['account_url'] = $feed_url;
        } else {
            $data['feed_url'] = $feed->feed_url;
        }
        
        unset($feed);
        
        if(!$data['account_url']) {
            $data['account_url'] = $data['feed_url'];
        }
        
        $data['service'] = 'RSS-Feed';
        $data['username'] = 'RSS-Feed';
        $data['service_type'] = $service_type;
        
        $items = $this->feed2array($username, 8, $data['service_type'], $data['feed_url']);
        
        $data['items'] = $items;
        
    	return $data;
    }
    
    /**
     * get service_type, feed_url and preview
     */
    public function getInfoFromService($username, $service_name, $account_username) {
        $services = Configure::read('services.data');
        $data = array();
        $data['service'] = $service_name;
        $data['username'] = $account_username;
        $data['service_type'] = $services[$service_name]['service_type'];
        
        $data['account_url'] = $this->getAccountUrl($service_name, $account_username);
        if($services[$service_name]['has_feed'] == 1) {
            $data['feed_url'] = $this->getFeedUrl($service_name, $account_username);
            $items = $this->feed2array($username, $service_name, $data['service_type'], $data['feed_url']);
        } else {
            $data['feed_url'] = '';
            $items = array();
        }
        
        $data['items'] = $items;
        
        return $data;
    }
    
    public function getAllServices() {
        if(file_exists(CACHE . 'models' . DS . 'noserub_services.php')) {
            eval(file_get_contents(CACHE . 'models' . DS . 'noserub_services.php'));
        } else {
            $this->createCache();
        }
    }
    
    /*
     *  Saves array of all services into a cache file.
     *
     * @return array
     */
    private function createCache() {
        $services = $this->getAllServicesForCache();
        if(!$services) {
            return false;
        }
        
        $data = "Configure::write('services', $services);";
        
        @file_put_contents(CACHE . 'models' . DS . 'noserub_services.php', $data);
        
        return $data;
    }
    
    /**
     * Loads all services in app/models/services/* and creates an array
     * out of them
     * 
     * @return array
     */
    private function getAllServicesForCache() {
        $files = @scandir(MODELS . 'services');
        if(!$files) {
            return false;
        }
        
        $data = array();
        $list = array();
        foreach($files as $file) {
            if(preg_match('/(.*)\.php/i', $file, $matches)) {
                $service_name = ucfirst($matches[1]);
                $class = $this->createService($service_name);
                $data[$service_name] = $class->getForCache();
                $list[$service_name] = $data[$service_name]['name'];
            }
        }
        
        $data['RSS-Feed'] = array(
            'name' => __('Any Site', true),
            'url' => '',
            'service_type' => 3,
            'icon' => 'rss.gif',
            'is_contact' => false,
            'has_feed' => true,
            'minutes_between_updates' => 30
        );
    	
        $services = array(
            'data' => $data,
            'list' => $list
        );
        
        return var_export($services, true);
    }
    
    private function createService($service_name) {
    	$class_name = $service_name . 'Service';
    	
    	if(!class_exists($class_name)) {
    		require_once(MODELS.'services'.DS.strtolower($service_name).'.php');
    	}
    	
    	return new $class_name();
    }
    
    private function createSimplePie($feed_url, $autodiscovery = false) {
    	App::import('Vendor', 'simplepie'.DS.'simplepie');
        $feed = new SimplePie();
        $feed->set_feed_url($feed_url);
        $feed->set_useragent(Configure::read('noserub.user_agent'));
        $feed->enable_cache(false);
        
        if($autodiscovery) {
            $autodiscovery_level = SIMPLEPIE_LOCATOR_ALL;
            $feed->force_feed(false); 
        } else {
            $autodiscovery_level = SIMPLEPIE_LOCATOR_NONE;
            $feed->force_feed(true); 
		}
        
        $feed->set_autodiscovery_level($autodiscovery_level);
        $feed->set_autodiscovery_cache_duration(0);
        
        return $feed;
    }
    
    /**
     * Factory method to create services
     */
    private function getService($service_name) {
    	// RSS feeds are not handled by this method, so we simply return false
    	if($service_name == 'RSS-Feed') {
    		return false;
    	}
    	
    	return $this->createService($service_name);
    }
}

/**
 * Base class for all services.
 */
abstract class AbstractService extends Object {
    protected $name;
    protected $url;
	protected $service_type;
	protected $icon;
	protected $has_feed;
	protected $is_contact;
	protected $minutes_between_updates;
	
	public function __construct() {
		$this->name = '';
		$this->url = '';
		$this->service_type = 0;
		$this->icon = '';
		$this->hasFeed = false;
		$this->isContact = false;
		$this->minutes_between_updates = 30;
		
		$this->init();
	}

	/**
	 * Implementations of this function have to return boolean false if 
	 * the service couldn't be detected, or a string with the username.
	 */
	public abstract function detectService($url);
	
	/**
	 * This method is called during constructing the object.
	 * Is used in derived classes to set the protected properties.
	 */
	public abstract function init();
	
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
	
	public function getForCache() {
	    return array(
	        'name' => $this->name,
	        'url' => $this->url,
	        'service_type' => $this->service_type,
	        'icon' => $this->icon,
	        'has_feed' => $this->has_feed,
	        'is_contact' => $this->is_contact,
	        'minutes_between_updates' => $this->minutes_between_updates
	    );
	}
	
	public function getName() {
        return $this->name;
	}
	
	public function getUrl() {
	    if(is_array($this->url)) {
	        // used for example in delicious.com / del.icio.us
	        return $this->url[0];
	    }
        return $this->url;
	}
	
	public function getServiceType() {
	    return $this->service_type;
	}
	
	public function getIcon() {
	    return $this->icon;
	}
	
	public function hasFeed() {
	    return $this->has_feed;
	}
	
	public function isContact() {
	    return $this->is_contact;
	}
	
	public function getMinutesBetweenUpdates() {
	    return $this->minutes_between_updates;
	}
	
	public function getAccountUrl($username) {
		return '';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	/**
	 * Get the create time of that item.
	 * For some services, get_date() returns
	 * the published date, therefore we need to
	 * make it overrideable.
	 *
	 * @param object $feeditem
	 *
	 * @return string
	 */
	public function getDatetime($feeditem) {
	  return $feeditem->get_date('Y-m-d H:i:s');
	}
	
	public function getTitle($feeditem) {
		$title = $feeditem->get_title();

		return $title ? $title : 'Untitled';
	}
	
	public function getFeedUrl($username) {
		return false;
	}	
}

class ServiceTypeFilterFactory {
	public static function getFilter($service_type) {
		if($service_type == 1) {
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
		App::import('Vendor', 'htmlpurifier', array('file' => 'htmlpurifier'.DS.'HTMLPurifier.standalone.php'));		
	}
	
	public function filter($item) {
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Cache.SerializerPath', CACHE . 'htmlpurifier');
		$config->set('HTML.Allowed', 'img[src|alt]');
        
		$purifier = new HTMLPurifier($config);
		$clean_html = $purifier->purify($item['content']);
		$clean_html = str_replace('<img src=', '<img width="75" height="75" src=', $clean_html);
		$img_src = substr($clean_html, stripos($clean_html, '<img '));
		$img_src = substr($img_src, 0, stripos($img_src, '>'));
		
		$item['content'] = $img_src;
		
		return $item;
	}
}