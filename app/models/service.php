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
    	// RSS feeds are not handled by this method, so we simply return false
    	if ($service_id == 8) {
    		return false;
    	}
    	
    	$this->recursive = 0;
    	$service = $this->find(array('Service.id' => $service_id), array('Service.internal_name'));
    	
    	if (!$service) {
    		return false;
    	}
    	
    	$serviceName = $service['Service']['internal_name'];
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