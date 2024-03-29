<?php
/* SVN FILE: $Id:$ */
 
class Entry extends AppModel {
    public $belongsTo = array(
        'Identity',
        'Account' => array(
            'foreignKey' => 'foreign_key',
            'conditions' => array('Entry.model' => 'account')
        ),
        'Group' => array(
            'counterCache' => true,
            'foreignKey' => 'foreign_key',
            'conditions' => array('Entry.model' => 'group')
        ),
        'Location' => array(
            'foreignKey' => 'foreign_key',
            'conditions' => array('Entry.model' => 'location')
        ),
        'Event' => array(
            'foreignKey' => 'foreign_key',
            'conditions' => array('Entry.model' => 'event')
        )
    );
    
    public $hasMany = array(
        'Comment', 
		'Favorite',
        'FavoritedBy' => array(
                'className' => 'Favorite',
                'joinTable' => 'favorites',
                'foreignKey' => 'entry_id',
                'associationForeignKey' => 'identity_id'
            )
    );
    
    public $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.@\_ ]+$/')),
                                'required' => array('rule' => 'notEmpty')));

    
    /**
     * Adding a new entry
     *
     * @param string $type of the entry
     * @param int $identity_id of the owner
     * @param int foreignKey
     * @param string $model
     * @param int $restricted
     *
     * @return bool
     */
    public function add($type, $data, $identity_id, $foreignKey = 0, $model = '', $restricted = false) {
        if($model != '' && $model != 'account' && 
           $model != 'group' && $model != 'location' && 
           $model != 'event') {
            return false;
        }
        
        if($model == 'group') {
            $this->Group->id = $foreignKey;
            if(!$this->Group->isSubscribed($identity_id)) {
                // this user is not subscribed to this group
                return false;
            }
        }

        switch($type) {
            case 'micropublish':
                return $this->addMicropublish($identity_id, $data['text'], $foreignKey, $model, $restricted);
            
            case 'link':
                return $this->addLink($identity_id, $data['description'], $data['url'], $foreignKey, $model, $restricted);
                
            case 'text':
                return $this->addText($identity_id, $data['title'], $data['text'], $foreignKey, $model, $restricted);
                
            case 'webcam':
                return $this->addPhoto($identity_id, $data['title'], $data['filename'], $foreignKey, $model, $restricted);
        }
        
        return false;
    }
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
        $this->Account->contain();
        $account = $this->Account->read();

        if($check_next_update &&
           $account['Account']['next_update'] > date('Y-m-d H:i:s')) {
            # no need for an update yet
            return array();
        }
        
        $identity_id = $account['Account']['identity_id'];
        $service_type = $account['Account']['service_type'];
        
        $account_data = $this->Account->getData();
        $entries = array();
        if($account_data) {
            # get date of newest item in db
            $entry_data = $this->find('all', array(
                'contain' => false,
                'conditions' => array(
                    'identity_id' => $identity_id,
                    'foreign_key' => $account_id,
                    'model' => 'account'
                ),
                'fields' => array(
                    'MAX(published_on)'
                )
            )); 
            if(!$entry_data[0][0]['MAX(published_on)']) {
                $date_newest_item = '2000-01-01 00:00:00';
            } else {
                $date_newest_item = $entry_data[0][0]['MAX(published_on)'];
            }

            # get the new items
            foreach($account_data as $item) {
                if($item['datetime'] >= $date_newest_item) {
                    $entry = $this->update($identity_id, $account_id, $service_type, $item);
                    if($entry) {
                        $entries[] = $entry; 
                   }
                }
            }
        }

        # update account
        $services = Configure::read('services.data');
        $minutes_between_updates = $services[$account['Account']['service']]['minutes_between_updates'];
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
    public function update($identity_id, $account_id, $service_type, $item) {
        # search, if there is an entry with the same url
        $this->contain();
        $this->cacheQueries = false;
        $entry = $this->find(
            'first',
            array(
                'conditions' => array(
                    'identity_id' => $identity_id,
                    'foreign_key' => $account_id,
                    'model' => 'account',
                    'url' => $item['url']
                )
            )
        );
        if($entry) {
            # check for update
            $entry = $entry['Entry'];
            if($entry['published_on'] < $item['datetime']) {
                # update
                $entry['published_on'] = $item['datetime'];
                $entry['title'] = $item['title'];
                $entry['content'] = $item['content'];
                $entry['latitude'] = $item['latitude'];
                $entry['longitude'] = $item['longitude'];
                $this->id = $entry['id'];
                $saveable = array(
                    'published_on', 'title', 'content',
                    'latitude', 'longitude');
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
                'identity_id' => $identity_id,
                'foreign_key' => $account_id,
                'model' => 'account',
                'service_type' => $service_type,
                'published_on' => $item['datetime'],
                'title' => $item['title'] ? $item['title'] : '',
                'url' => $item['url'] ? $item['url'] : '',
                'uid' => $item['url'] ? md5($item['url']) : '',
                'content' => $item['content'] ? $item['content'] : '',
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
                'restricted' => !$frontpage_updates
            );
            $this->save($entry);
            $entry['id'] = $this->id;
            if(!$entry['uid']) {
                # now set url and uid
                $url = Router::url('/entry/' . $this->id . '/', true);
                $this->saveField('url', $url);
                $this->saveField('uid', md5($url));
            }
        }
        
        return $entry;
    }
    
    /**
     */
    public function getForDisplay($filter, $limit, $with_restricted = false) {
        if(!Configure::read('NoseRub.manual_feeds_update')) {
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
                'Identity.firstname',
                'Identity.lastname',
                'Identity.username',
            	'Identity.photo',
            	'Identity.sex',
                'FavoritedBy',
                'Comment'
            )
        );
        
        App::import('Model', 'ServiceType');
        $this->ServiceType = new ServiceType();
        $conditions = array();
        if(isset($filter['account_id'])) {            
            $conditions['foreign_key'] = $filter['account_id'];
            $conditions['model'] = 'account';
        }
        if(isset($filter['group_id'])) {            
            $conditions['foreign_key'] = $filter['group_id'];
            $conditions['model'] = 'group';
        }
        if(isset($filter['location_id'])) {            
            $conditions['foreign_key'] = $filter['location_id'];
            $conditions['model'] = 'location';
        }
        if(isset($filter['event_id'])) {            
            $conditions['foreign_key'] = $filter['event_id'];
            $conditions['model'] = 'event';
        }
        if(isset($filter['filter'])) {
            $ids = $this->ServiceType->getList($filter['filter']);
            if($ids) {
                $conditions['service_type'] = $ids;
            }
        }
        if(isset($filter['identity_id'])) {
            $conditions['identity_id'] = $filter['identity_id'];
        }
		if(isset($filter['entry_id'])) {
            $conditions['Entry.id'] = $filter['entry_id'];
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
        if(isset($filter['favorited_by'])) {
            # get last favorited entry_ids
            $this->FavoritedBy->contain();
            $favorites = $this->FavoritedBy->find(
                'all',
                array(
                    'conditions' => array(
                        'FavoritedBy.identity_id' => $filter['favorited_by']
                    ),
                    'order' => 'FavoritedBy.created DESC',
                    'limit' => $limit
                )
            );
            
            $entry_ids = Set::extract($favorites, '{n}.FavoritedBy.entry_id');
            $conditions['Entry.id'] = $entry_ids;
        } 
        if(isset($filter['commented_by'])) {
			# get last commented entry_ids
			$this->Comment->contain();
			$comments = $this->Comment->find(
				'all',
				array(
					'conditions' => array(
						'Comment.identity_id' => $filter['commented_by']
					),
					'order' => 'Comment.published_on',
					'limit' => $limit
				)
			);
			
			$entry_ids = Set::extract($comments, '{n}.Comment.entry_id');
			$conditions['Entry.id'] = $entry_ids;
		}
		
        $new_items = $this->Identity->Entry->find(
            'all',
            array(
                'conditions' => $conditions,
                'order'      => 'Entry.published_on DESC',
                'limit'      => $limit
            )
        );
        
        foreach($new_items as $idx => $data) {
            $data = $this->Identity->addIdentity('FavoritedBy', $data);
            $data = $this->Identity->addIdentity('Comment', $data);
            
            if($data['Entry']['service_type'] == ServiceType::MICROPUBLISH && 
               $data['Entry']['model'] == 'account' && 
               $data['Entry']['foreign_key'] > 0) {
                $data['Entry']['title'] = $data['Entry']['content'] = $this->micropublishMarkup($data['Entry']['title']);
            }
            $data['Identity']['photo'] = $this->Identity->getPhotoUrl($data, 'Identity', true);
            
            $new_items[$idx] = $data;
        }
        
        return $new_items;
    }
    
    /**
     * Deletes all entries for given account_id
     *
     * @param  $account_id for which all entries should be removed
     * @return 
     * @access 
     */
    public function deleteByAccountId($account_id) {
        $data = $this->find(
            'all',
            array(
                'contain' => false,
                'conditions' => array(
                    'Entry.foreign_key' => $account_id,
                    'Entry.model' => 'account'
                ),
                'fields' => 'Entry.id'
        ));
        foreach($data as $item) {
            $this->id = $item['Entry']['id'];
            $this->Comment->deleteByEntryId($this->id);
            $this->delete();
        }
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
                'identity_id' => $identity_id,
                'foreign_key' => 0,
                'model' => '',
                'service_type' => ServiceType::LOCATION,
                'published_on' => date('Y-m-d H:i:s'),
                'title' => $location['Location']['name'],
                'url' => '',
                'uid' => '',
                'content' => $location['Location']['name'],
                'restricted' => $restricted
            );
                      
            $this->create();
            if($this->save($data)) {    
                # now set url and uid
                $url = Router::url('/entry/' . $this->id . '/', true);
                $this->saveField('url', $url);
                $this->saveField('uid', md5($url));
            } else {
                $this->log(print_r($data, true), LOG_ERROR);
            }
            
            #App::import('Model', 'Xmpp');
            #$this->Xmpp = new Xmpp();
            #$message = $this->getMessage($data);
            #$this->Xmpp->broadcast($message);
    
            return true;
        } else {
            return false;
        }
    }
    
    private function addMicropublish($identity_id, $text, $foreignKey = 0, $model = '', $restricted = false) {
        if(is_null($restricted)) {
            $restricted = $this->getRestricted($identity_id);
        }
        $text = htmlspecialchars(strip_tags($text), ENT_QUOTES, 'UTF-8');
        $text = $this->shorten($text);
        
        $with_markup = $this->micropublishMarkup($text);
        
        $data = array(
            'identity_id' => $identity_id,
            'foreign_key' => $foreignKey,
            'model' => $model,
            'service_type' => ServiceType::MICROPUBLISH,
            'published_on' => date('Y-m-d H:i:s'),
            'title' => $with_markup,
            'url' => '',
            'uid' => '',
            'content' => $text,
            'restricted' => $restricted
        );
        
        $this->create();
        if($this->save($data)) {    
            # now set url and uid
            $url = Router::url('/entry/' . $this->id . '/', true);
            $this->saveField('url', $url);
            $this->saveField('uid', md5($url));
        } else {
            $this->log('could not save entry (addMicropublish):', LOG_ERROR);
            $this->log(print_r($data, true), LOG_ERROR);
        }
        
        $this->sendToTwitter($identity_id, $text);
        $this->sendToOmb($identity_id, $this->id, $text);
        
        return true;
    }
    
    public function addOmbNotice($identity_id, $notice_url, $notice) {
        $restricted = $this->getRestricted($identity_id);
    	$text = htmlspecialchars(strip_tags($notice), ENT_QUOTES, 'UTF-8');
        $text = $this->shorten($text);
        
        $with_markup = $this->micropublishMarkup($text);
        
        $data = array(
            'identity_id' => $identity_id,
            'foreign_key' => 0,
            'model' => '',
            'service_type' => ServiceType::MICROPUBLISH,
            'published_on'  => date('Y-m-d H:i:s'),
            'title' => $with_markup,
            'url' => $notice_url,
            'uid' => md5($notice_url),
            'content' => $text,
            'restricted' => $restricted
        );
        
        $this->create();
        $this->save($data);
        
        return true;
    }
    
    /**
     * adds html tags for links and #
     *
     * @param string $text
     * 
     * @return string
     */
    public function micropublishMarkup($text) {
        # make links clickable
        $pattern = '/((?:https?:\/\/|ftp:\/\/|mailto:|news:)[^\s]+)/i';
        $text = preg_replace($pattern, "<a href=\"\\1\">\\1</a>", $text);
        
        # change hashtags into searches
        $pattern = '/#([\wäöüÄÖÜß]*)/i';
        $text = preg_replace($pattern, "<a href=\"" . Router::url('/search/') . "?q=%23\\1\">#\\1</a>", $text);
        
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
    public function shorten($text, $max_length = 140) {
        $text = $this->shortenUrlInText($text);
        
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
     *
     * @return string
     */
    private function shortenUrlInText($text) {
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
    
    private function addLink($identityId, $description, $url, $foreignKey = 0, $model = '', $restricted = false) {
        App::import('Vendor', 'UrlUtil');
    	
    	if(is_null($restricted)) {
            $restricted = $this->getRestricted($identityId);
        }
        $url = UrlUtil::addHttpIfNoProtocolSpecified($url);
        $data = array(
            'identity_id' => $identityId,
            'foreign_key' => $foreignKey,
            'model' => $model,
            'service_type' => ServiceType::LINK,
            'published_on' => date('Y-m-d H:i:s'),
            'title' => $description,
            'url' => $url,
            'uid' => md5($url),
            'content' => '<a href="' . $url . '">' . $description . '</a>',
            'restricted' => $restricted
        );
        
        $this->create();
        return $this->save($data);
    }
    
    private function addText($identity_id, $title, $text, $foreignKey = 0, $model = '', $restricted = false) {
        if(is_null($restricted)) {
            $restricted = $this->getRestricted($identity_id);
        }
        $data = array(
            'identity_id' => $identity_id,
            'foreign_key' => $foreignKey,
            'model' => $model,
            'service_type' => ServiceType::TEXT,
            'published_on' => date('Y-m-d H:i:s'),
            'title' => $title,
            'url' => '',
            'uid' => '',
            'content' => $text,
            'restricted' => $restricted
        );
        
        $this->create();
        if($this->save($data)) {    
            # now set url and uid
            $url = Router::url('/entry/' . $this->id . '/', true);
            $this->saveField('url', $url);
            $this->saveField('uid', md5($url));
        } else {
            return false;
        }
        
        return true;
    }
    
    private function addPhoto($identity_id, $title, $filename, $foreignKey = 0, $model = '', $restricted = false) {
        if(is_null($restricted)) {
            $restricted = $this->getRestricted($identity_id);
        }
        
        $raw_content = array(
            'photo' => Router::url('/static/photos/' . $filename, true),
            'thumb' => Router::url('/static/photos/' . $filename, true) // we currently do not resize
        );
        
        $data = array(
            'identity_id' => $identity_id,
            'foreign_key' => $foreignKey,
            'model' => $model,
            'service_type' => ServiceType::PHOTO,
            'published_on' => date('Y-m-d H:i:s'),
            'title' => $title,
            'url' => '',
            'uid' => '',
            'content' => $raw_content,
            'restricted' => $restricted
        );
        
        $this->create();
        if($this->save($data)) {    
            # now set url and uid
            $url = Router::url('/entry/' . $this->id . '/', true);
            $this->saveField('url', $url);
            $this->saveField('uid', md5($url));
        } else {
            return false;
        }
        
        return true;
    }
    
    public function addNoseRub($identity_id, $value, $restricted = false) {
        if(is_null($restricted)) {
            $restricted = $this->getRestricted($identity_id);
        }
        $data = array(
            'identity_id' => $identity_id,
            'foreign_key' => 0,
            'model' => '',
            'service_type' => ServiceType::NOSERUB,
            'published_on' => date('Y-m-d H:i:s'),
            'title' => $value,
            'url' => '',
            'uid' => '',
            'content' => $value,
            'restricted' => $restricted
        );
        
        $this->create();
        if($this->save($data)) {    
            # now set url and uid
            $url = Router::url('/entry/' . $this->id . '/', true);
            $this->saveField('url', $url);
            $this->saveField('uid', md5($url));
        } else {
            $this->log('could not save entry (addNoseRub):', LOG_ERROR);
            $this->log(print_r($data, true), LOG_ERROR);
        }
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
    public function addNewService($identity_id, $service_name, $restricted = false) {
        $service_name = Configure::read('services.list.' . $service_name);
        
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
    
    public function addFavorite($identity_id, $entry_id, $restricted = false) {
        $this->Identity->Entry->id = $entry_id;
        $title = strip_tags($this->Identity->Entry->field('title'));
        $link = '<a href="' . Router::url('/entry/' . $entry_id . '/') . '">' . $title . '</a>';
        $message = sprintf(__('marked a new favorite: %s', true), $link);
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    
    public function addComment($identity_id, $entry_id, $restricted = false) {
        $this->Identity->Entry->id = $entry_id;
        $title = strip_tags($this->Identity->Entry->field('title'));
        $link = '<a href="' . Router::url('/entry/' . $entry_id . '/') . '">' . $title . '</a>';
        $message = sprintf(__('commented on: %s', true), $link);
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    
    public function addGroup($identity_id, $group_id, $restricted = false) {
        $this->Group->id = $group_id;
        $slug = $this->Group->field('slug');
        $name = $this->Group->field('name');
        
        $message = sprintf(__('created a new group: %s', true), '<a href="' . Router::url('/groups/view/' . $slug) . '">' . $name . '</a>');
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    
    public function addLocation($identity_id, $location_id, $restricted = false) {
        $this->Location->id = $location_id;
        $slug = $this->Location->field('slug');
        $name = $this->Location->field('name');
        
        $message = sprintf(__('created a new location: %s', true), '<a href="' . Router::url('/locations/view/' . $location_id . '/' . $slug) . '">' . $name . '</a>');
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    
    public function addEvent($identity_id, $event_id, $restricted = false) {
        $this->Event->id = $event_id;
        $slug = $this->Event->field('slug');
        $name = $this->Event->field('name');
        
        $message = sprintf(__('created a new event: %s', true), '<a href="' . Router::url('/events/view/' . $event_id . '/' . $slug) . '">' . $name . '</a>');
        $this->addNoseRub($identity_id, $message, $restricted);
    }
    
    public function getMessage($entry) {
        $this->Identity->contain();
        $fields = array(
            'Identity.firstname',
            'Identity.lastname',
            'Identity.username'
        );
        $identity = $this->Identity->findById($entry['identity_id'], $fields);
        
        $service_types = Configure::read('service_types');
        $service_type = $service_types[$entry['service_type']];
        
        $splitted = split('/', $identity['Identity']['username']);
        $splitted2 = split('@', $splitted[count($splitted)-1]);
        $username = $splitted2[0];
        $intro = str_replace('@user@', 'http://'.$identity['Identity']['username'], $service_type['intro']);
        $intro = str_replace('@item@', '» '.$entry['title'].' «', $intro);
        
        return $intro;
    }
    
    public function updateRestriction($identity_id, $restricted) {
        $sql = 'UPDATE ' . $this->tablePrefix . 'entries SET restricted=' . $restricted . ' WHERE identity_id=' . $identity_id;
        $this->query($sql);
    }
    
    /**
     * tries to get an entry by it's uid. checks for the url, too, to
     * avoid hash collisions (although very unlikely)
     *
     * returns an array, when more than one entry with that uid was
     * found - this can happen when different identities subscribed
     * the same feeds. 
     *
     * @param string $uid
     * @param string $url
     *
     * @return array
     */
    public function getByUid($uid, $url = null) {
        if(!$uid) {
            # normally this shouldn't happen, but you never know...
            return false;
        }
        
        $conditions = array('Entry.uid' => $uid);
        if($url) {
            $conditions['Entry.url'] = $url;
        }
        $this->contain();
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Entry.uid' => $uid
                )
            )
        );
    }
    
    public function getUid($entry_id) {
    	$this->id = $entry_id;
        
    	return $this->field('uid');
    }
    
    /**
     * - When Entry.content is an array => serialize and encode it
     * - When Entry belongs to a group => update group's last activity
     */
    public function beforeSave() {
        if(isset($this->data['Entry']['content']) && is_array($this->data['Entry']['content'])) {
            $this->data['Entry']['content'] = $this->serializeAndEncode($this->data['Entry']['content']);
        } else if(isset($this->data['content']) && is_array($this->data['content'])) {
            $this->data['content'] = $this->serializeAndEncode($this->data['content']);
        }
        
        return parent::beforeSave();
    }
    
    /**
     * - When created => set last_activity, this also updates group's last activity
     */
    public function afterSave($created) {
        if($created) {
            $this->updateLastActivity();
        }
        
        return parent::afterSave($created);
    }
    
    /**
     * if this entry belongs to a group, also update
     * this group's last activity.
     */
    public function updateLastActivity() {
        if(!$this->exists()) {
            return false;
        }
        $this->saveField('last_activity', date('Y-m-d H:i:s'));
        
        $model = $this->field('model');
        if($model == 'group') {
            $this->Group->id = $this->field('foreign_key');
            $this->Group->updateLastActivity();
        }
    }
    
    private function serializeAndEncode($data) {
        return @base64_encode(@serialize($data));
    }
    
    private function sendToOmb($identity_id, $entry_id, $text) {
    	App::import('Component', 'OmbRemoteService');
    	OmbRemoteServiceComponent::createRemoteService()->postNotice($identity_id, $entry_id, $text);
    }
    
    /**
     * send status to twitter 
     */
    private function sendToTwitter($identity_id, $text) {
    	return $this->Identity->TwitterAccount->updateStatus($identity_id, $text);
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
