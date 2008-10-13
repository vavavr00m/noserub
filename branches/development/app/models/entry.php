<?php
/* SVN FILE: $Id:$ */
 
class Entry extends AppModel {
    public $belongsTo = array('Identity', 'Account', 'ServiceType');
    
    public $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.@\_ ]+$/')),
                                'required' => VALID_NOT_EMPTY));

    
    /**
     * updates the entries
     *
     * @param int $account_id
     * @param bool $check_next_update
     * 
     * @return array added entries
     */
    public function updateByAccountId($account_id, $check_next_update = false) {
        $this->Account->id = $account_id;
        $this->Account->contain(array('Service.minutes_between_updates'));
        $account = $this->Account->read();

        if($check_next_update &&
           $account['Account']['next_update'] > date('Y-m-d H:i:s')) {
            # no need for an update yet
            return array();
        }
        
        $identity_id     = $account['Account']['identity_id'];
        $service_type_id = $account['Account']['service_type_id'];
        
        $account_data = $this->Account->getData();
        $entries = array();
        if($account_data) {
            # get date of newest item in db
            $this->contain();
            $conditions = array(
                'identity_id' => $identity_id,
                'account_id'  => $account_id
            );
            $fields = array('MAX(published_on)');
            $entry_data = $this->find(
                'all',
                array(
                    'conditions' => array(
                        'identity_id' => $identity_id,
                        'account_id'  => $account_id
                    ),
                    'fields' => array(
                        'MAX(published_on)'
                    )
                )
            ); 

            if(!$entry_data[0][0]['MAX(published_on)']) {
                $date_newest_item = '2000-01-01 00:00:00';
            } else {
                $date_newest_item = $entry_data[0][0]['MAX(published_on)'];
            }

            # get the new items
            foreach($account_data as $item) {
                if($item['datetime'] >= $date_newest_item) {
                    $entry = $this->update($identity_id, $account_id, $service_type_id, $item);
                    if($entry) {
                        $entries[] = $entry; 
                   }
                }
            }
        }

        # update account
        $minutes_between_updates = $account['Service']['minutes_between_updates'];
        if(!$minutes_between_updates) {
            # this account is not properly attached to a service
            $minutes_between_updates = 360; 
        }
        $next_update = date('Y-m-d H:i:s', strtotime('+' . $minutes_between_updates . ' minutes'));
        $this->Account->id = $account_id;
        $this->Account->saveField('next_update', $next_update);
        
        return $entries;
    }

    /**
     * updates/creates one single entry
     */
    public function update($identity_id, $account_id, $service_type_id, $item) {
        # search, if there is an entry with the same url
        $this->contain();
        $this->cacheQueries = false;
        $entry = $this->find(
            'first',
            array(
                'conditions' => array(
                    'identity_id' => $identity_id,
                    'account_id'  => $account_id,
                    'url'         => $item['url']
                )
            )
        );
        
        if($entry) {
            # check for update
            $entry = $entry['Entry'];
            if($entry['published_on'] < $item['datetime']) {
                # update
                $entry['published_on'] = $item['datetime'];
                $entry['title']        = $item['title'];
                $entry['content']      = $item['content'];
                $this->id = $entry['id'];
                $saveable = array('published_on', 'title', 'content');
                $this->save($entry, $saveable, true);
            } else {
                # needs no update
                $entry = false;
            }
        } else {
            # create
            $this->create();
            
            # find out, wether we need to restrict this entry.
            # right now, we only look for the general setting
            # in the identity.
            $this->Identity->id = $identity_id;
            $frontpage_updates = $this->Identity->field('frontpage_updates');
            
            $entry = array(
                'identity_id'     => $identity_id,
                'account_id'      => $account_id,
                'service_type_id' => $service_type_id,
                'published_on'    => $item['datetime'],
                'title'           => $item['title'] ? $item['title'] : '',
                'url'             => $item['url'] ? $item['url'] : '',
                'content'         => $item['content'] ? $item['content'] : '',
                'restricted'      => !$frontpage_updates
            );
            $saveable = array_keys($entry);
            $this->save($entry, $saveable, true);
            $entry['id'] = $this->id;
        }
        
        return $entry;
    }
    
    /**
     */
    public function getForDisplay($filter, $limit, $with_restricted = false) {
        if(!NOSERUB_MANUAL_FEEDS_UPDATE) {
            # update it before getting data
            if(isset($filter['identity_id']) && $filter['identity_id']) {
                $this->Account->contain();
                $fields = array('id');
                $data = $this->Account->findAllByIdentityId(
                	$filter['identity_id'],
                	$fields
                );
                foreach($data as $item) {
                	$this->updateByAccountId($item['Account']['id'], true);
                }
            }
        }
        
        $this->Identity->Entry->contain(
            array(
                'ServiceType.token',
                'ServiceType.intro',
                'Identity.firstname',
                'Identity.lastname',
                'Identity.username'
            )
        );
        $conditions = array();
        if(isset($filter['account_id'])) {            
            $conditions['account_id'] = $filter['account_id'];
        }
        if(isset($filter['filter'])) {
            $ids = $this->ServiceType->getList($filter['filter']);
            if($ids) {
                $conditions['service_type_id'] = $ids;
            }
        }
        if(isset($filter['identity_id'])) {
            $conditions['identity_id'] = $filter['identity_id'];
        }
        if(isset($filter['search']) && $filter['search']) {
            $terms = split(' ', $filter['search']);
            $search_conditions = array();
            foreach($terms as $term) {
                $search_conditions[] = '(Entry.title LIKE "%'.$term.'%" OR Entry.content LIKE "%'.$term.'%")';
            }
            $conditions[] = '(' . join(' AND ', $search_conditions) . ')';
        }
        if(!$with_restricted) {
            # but only if no identity_id is given. we want to display the
            # entries from a specific identity all the time on their own
            # profile page
            $num_identities = 0;
            if(isset($filter['identity_id'])) {
                $num_identities = count($filter['identity_id']);
            }
            if($num_identities != 1) {
                $conditions['restricted'] = 0;                
            }
        }
        
        $new_items = $this->Identity->Entry->find(
            'all',
            array(
                'conditions' => $conditions,
                'order'      => 'Entry.published_on DESC',
                'limit'      => $limit
            )
        );
    
        return $new_items;
    }
    
    /**
     * New entry after setting a location
     *
     * @param int $identity_id
     * @param array $location
     * @param bool $restricted wether to show this entry on the
     *             global social stream
     * @return bool
     */
    public function setLocation($identity_id, $location, $restricted = false) {
        if(is_null($restricted)) {
            $restricted = $this->getRestricted($identity_id);
        }
        if($location && 
           isset($location['Location']['name']) && 
           $location['Location']['name'] != '') {
            $data = array(
                'identity_id'     => $identity_id,
                'account_id'      => 0,
                'service_type_id' => 9,
                'published_on'    => date('Y-m-d H:i:s'),
                'title'           => $location['Location']['name'],
                'url'             => '',
                'content'         => $location['Location']['name'],
                'restricted'      => $restricted
            );
                      
            $this->create();
            $this->save($data);
            
            #App::import('Model', 'Xmpp');
            #$this->Xmpp = new Xmpp();
            #$message = $this->getMessage($data);
            #$this->Xmpp->broadcast($message);
    
            return true;
        } else {
            return false;
        }
    }
    
    public function addMicropublish($identity_id, $text, $restricted = false) {
        if(is_null($restricted)) {
            $restricted = $this->getRestricted($identity_id);
        }
        $text = htmlspecialchars(strip_tags($text), ENT_QUOTES, 'UTF-8');
        $text = $this->shorten($text, 140);
        
        $with_markup = $this->micropublishMarkup($text);
        
        $data = array(
            'identity_id'     => $identity_id,
            'account_id'      => 0,
            'service_type_id' => 5,
            'published_on'    => date('Y-m-d H:i:s'),
            'title'           => $with_markup,
            'url'             => '',
            'content'         => $text,
            'restricted'      => $restricted
        );
        
        $this->create();
        $this->save($data);
        
        $this->sendToTwitter($identity_id, $text);
        $this->sendToOmb($identity_id, $text);
        
        return true;
    }
    
    /**
     * adds html tags for links and @. also cuts after 160 chars.
     *
     * @param string $text
     * 
     * @return string
     */
    public function micropublishMarkup($text) {
        # make links clickable
        $pattern = '/((?:https?:\/\/|ftp:\/\/|mailto:|news:)[^\s]+)/i';
        $text = preg_replace($pattern,"<a href=\"\\1\">\\1</a>", $text);
        
        return $text;
    }
    
    /**
     * shortens a text to $max_length by shortening urls
     * and cutting
     *
     * @param string $text
     * @param int $max_length
     *
     * @return string
     */
    public function shorten($text, $max_length) {
        if(strlen($text) > $max_length) {
            $text = $this->shortenUrlInText($text);
        }
        
        if(strlen($text) > $max_length) {
            # cut after $max_length chars
            $text = substr($text, 0, $max_length);
        }
        
        return $text;
    }
    
    /**
     * shortens all urls in a given text
     *
     * @param string $text
     * @param int $max_length
     *
     * @return string
     */
    public function shortenUrlInText($text) {
        $pattern = '/((?:https?:\/\/|ftp:\/\/|mailto:|news:)[^\s]+)/i';
        if(preg_match_all($pattern, $text, $matches)) {
            App::import('Vendor', 'WebExtractor');
        	foreach($matches[0] as $url) {
                $token = WebExtractor::fetchUrl('http://create.li.ttle.de/?url=' . urlencode($url));
                $new_url = 'http://li.ttle.de/' . $token;
                if(strlen($new_url) < strlen($url)) {
                    $text = str_replace($url, $new_url, $text);
                }
            }
        }
        return $text;
    }
    
    public function addNoseRub($identity_id, $value, $restricted = false) {
        if(is_null($restricted)) {
            $restricted = $this->getRestricted($identity_id);
        }
        $data = array(
            'identity_id'     => $identity_id,
            'account_id'      => 0,
            'service_type_id' => 0,
            'published_on'    => date('Y-m-d H:i:s'),
            'title'           => $value,
            'url'             => '',
            'content'         => $value,
            'restricted'      => $restricted
        );
        
        $this->create();
        $this->save($data);
        
        return true;
    }
    
    /**
     * Add a NoseRub message, that someone changed the profile photo
     */
    public function addPhotoChanged($identity_id, $restricted = false) {
        $message = 'set a new profile photo';
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    
    /**
     * Add a NoseRub message, that someone added a new service
     */
    public function addNewService($identity_id, $service_id, $restricted = false) {
        $this->Account->Service->contain();
        $this->Account->Service->id = $service_id;
        $service_name = $this->Account->Service->field('name');
        
        $message = 'added a new service: ' . $service_name;
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    
    public function addNewContact($identity_id, $with_identity_id, $restricted = false) {
        $this->Identity->contain();
        $this->Identity->id = $with_identity_id;
        $data = $this->Identity->read();
        
        $message = 'added a new contact: <a href="http://' . $data['Identity']['username'] . '">' . $data['Identity']['local_username'] .'</a>';
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    /**
     */
    public function getMessage($entry) {
        $this->Identity->contain();
        $fields = array(
            'Identity.firstname',
            'Identity.lastname',
            'Identity.username'
        );
        $identity = $this->Identity->findById($entry['identity_id'], $fields);
        
        $this->ServiceType->contain();
        $fields = array(
            'ServiceType.token',
            'ServiceType.intro'
        );
        $service_type = $this->ServiceType->findById($entry['service_type_id'], $fields);
        
        $splitted = split('/', $identity['Identity']['username']);
        $splitted2 = split('@', $splitted[count($splitted)-1]);
        $username = $splitted2[0];
        $intro = str_replace('@user@', 'http://'.$identity['Identity']['username'], $service_type['ServiceType']['intro']);
        $intro = str_replace('@item@', '» '.$entry['title'].' «', $intro);
        
        return $intro;
    }
    
    public function updateRestriction($identity_id, $restricted) {
        $sql = 'UPDATE ' . $this->tablePrefix . 'entries SET restricted=' . $restricted . ' WHERE identity_id=' . $identity_id;
        $this->query($sql);
    }
    
    private function sendToOmb($identity_id, $text) {
    	$ombServiceAccessToken = ClassRegistry::init('OmbServiceAccessToken');
    	$accessToken = $ombServiceAccessToken->findByIdentityId($identity_id);
    	
    	if (!$accessToken) {
    		return;
    	}
    	
    	App::import('Component', array('OmbConsumer', 'OauthConsumer'));
    	$ombConsumer = new OmbConsumerComponent();
    	$ombConsumer->OauthConsumer = new OauthConsumerComponent();
    	$ombConsumer->postNotice($accessToken['OmbServiceAccessToken']['token_key'], $accessToken['OmbServiceAccessToken']['token_secret'], $accessToken['OmbService']['post_notice_url'], $text);
    }
    
    /**
     * send status to twitter 
     *
     * many thanks to laconi.ca for this code!
     * can be found in lib/util.php there.
     */
    private function sendToTwitter($identity_id, $text) {   
        if(defined('NOSERUB_ALLOW_TWITTER_BRIDGE') &&
           !NOSERUB_ALLOW_TWITTER_BRIDGE) {
            return;       
        }

        $this->Identity->TwitterAccount->contain();
        $data = $this->Identity->TwitterAccount->findByIdentityId($identity_id);
        if($data['TwitterAccount']['bridge_active'] != 1) {
            return;
        }
             
    	$twitter_username = $data['TwitterAccount']['username'];
    	$twitter_password = $data['TwitterAccount']['password'];
    	$uri = 'http://www.twitter.com/statuses/update.json';

    	$options = array(
    		CURLOPT_USERPWD 		=> $twitter_username . ':' . $twitter_password,
    		CURLOPT_POST			=> true,
    		CURLOPT_POSTFIELDS		=> array(
    									'status'	=> $text,
    									'source'	=> 'noserub'
    									),
    		CURLOPT_RETURNTRANSFER	=> true,
    		CURLOPT_FAILONERROR		=> true,
    		CURLOPT_HEADER			=> false,
    		CURLOPT_USERAGENT		=> NOSERUB_USER_AGENT,
    		CURLOPT_CONNECTTIMEOUT	=> 20,  
    		CURLOPT_TIMEOUT			=> 20
    	);

        $safe_mode = ini_get('safe_mode');
        $open_basedir = ini_get('open_basedir');
        if(!$safe_mode && !$open_basedir) {
            # this option only works when safe_mode or open_basedir are not enabled
            $options[CURLOPT_FOLLOWLOCATION] = true;
            $options[CURLOPT_MAXREDIRS]      = 5; # just randomly picked to restrict it somehow
        }
        
        # ignoring all error messages or return values...
    	$ch = curl_init($uri);
        curl_setopt_array($ch, $options);
        curl_exec($ch);
    }
    
    /**
     * Right now, entries are only restricted, when the user did
     * not set the "show updates on frontpage" flag.
     * This is tested here, so we don't need to do it everytime in the
     * controllers
     *
     * @param int $identity_id
     *
     * @return int $restricted (0 or 1)
     */
    private function getRestricted($identity_id) {
        $this->Identity->id = $identity_id;
        return (($this->Identity->field('frontpage_updates') == 1) ? 0 : 1);
    }
}