<?php
/* SVN FILE: $Id:$ */
 
class Identity extends AppModel {
	public $hasOne = array('TwitterAccount');
    public $hasMany = array(
        'Account', 'Contact', 'ContactType', 'Consumer', 
        'OpenidSite', 'Location', 'Syndication', 'Entry'
    );
    
    public $belongsTo = array(
        'Location' => array('className'  => 'Location',
                            'foreignKey' => 'last_location_id'),
        'Network'
    );
    
    public $hasAndBelongsToMany = array(
            'FavoriteEntries' => array(
                'className' => 'Favorite',
                'joinTable' => 'favorites',
                'foreignKey' => 'identity_id',
                'associationForeignKey' => 'entry_id'
            ),
            // 'SubscribedGroup' => array(
            //                 'className'  => 'Group',
            //                 'joinTable'  => 'group_subscriptions',
            //                 'foreignKey' => 'identity_id',
            //                 'associationForeignKey' => 'group_id'
            //             ),
            //             'AdministratingGroup' => array(
            //                 'className' => 'Group',
            //                 'joinTable' => 'group_admins',
            //                 'foreignKey' => 'identity_id',
            //                 'associationForeignKey' => 'group_id' 
            //             ),
            //             'SubscribedNetwork' => array(
            //                 'className' => 'Network',
            //                 'joinTable' => 'network_subscriptions',
            //                 'foreignKey' => 'identity_id',
            //                 'associationForeignKey' => 'network_id'
            //             )
    );
    
    public $validate = array(
            'username' => array('content'  => array('rule' => array('custom', NOSERUB_VALID_USERNAME)),
                                'unique'   => array('rule' => 'validateUniqueUsername'),
                                'required' => VALID_NOT_EMPTY),
            'email'    => array('mail'       => VALID_EMAIL,
                                'required'   => VALID_NOT_EMPTY,
                                'restricted' => array('rule' => 'validateRestrictedEmail')),
            'passwd'   => array('rule' => array('minLength', 6)),
            'passwd2'  => array('rule' => 'validatePasswd2'),
    		'openid'   => array('rule' => 'validateUniqueOpenID')
        );
    
    public function validatePasswd2($value, $params = array()) {
        if ($this->data['Identity']['passwd'] !== $value['passwd2']) {
            return false;
        } else {
            return true;
        }
    }

    public function validateUniqueOpenID($value, $params = array()) {
    	if ($this->hasAny(array('Identity.openid' => $value['openid'], 'Identity.hash <>' => '#deleted#'))) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    /**
     * validate, if the username is already taken 
     */
    public function validateUniqueUsername($value, $params = array()) {
        App::import('Vendor', 'UsernameUtil');
    	$value = strtolower($value['username']);
        $split_username = $this->splitUsername($value);
        if(UsernameUtil::isReservedUsername($split_username['username'])) {
            return false;
        }

        if($this->hasAny(array('Identity.username' => $value))) {
            return false;
        } else {
            return true;
        }
    }    
    
    /**
     * check, whether host of email address matches context.network.registration_restricted_hosts
     */
    public function validateRestrictedEmail($email, $params = array()) {
        if (Configure::read('context.network.registration_restricted_hosts') == false ||
            $email == '') {
            return true;
        }
        list($local, $host) = explode('@', $email['email']);
        return in_array($host, explode(' ', Configure::read('context.network.registration_restricted_hosts')));
    }
    
    public function afterFind($data) {
        # moved from Identity Model to here, so that all the associated
        # afterFinds could be catched, too
        if(is_array($data)) {
            if(isset($data['username'])) {
                $username = Identity::splitUsername($data['username'], false);
                $data['local_username']  = $username['local_username'];
                $data['single_username'] = $username['single_username'];
                $data['username']        = $username['username'];
                $data['namespace']       = $username['namespace'];
                $data['local']           = $username['local'];
                $data['servername']      = $username['servername'];
                $data['name']            = trim($data['firstname'] . ' ' . $data['lastname']);
                if(!$data['name']) {
                    $data['name'] = $username['local_username'];
                }
            } else {
                foreach($data as $key => $item) {
                    $checkModels = array('WithIdentity', 'Identity');
                    foreach($checkModels as $modelName) {
                        if(isset($item[$modelName]['username'])) {
                            $username = Identity::splitUsername($item[$modelName]['username'], false);
                            $item[$modelName]['local_username']  = $username['local_username'];
                            $item[$modelName]['single_username'] = $username['single_username'];
                            $item[$modelName]['username']        = $username['username'];
                            $item[$modelName]['namespace']       = $username['namespace'];
                            $item[$modelName]['local']           = $username['local'];
                            $item[$modelName]['servername']      = $username['servername'];
                            $item[$modelName]['name']            = trim($item[$modelName]['firstname'] . ' ' . $item[$modelName]['lastname']);
                            if(!$item[$modelName]['name']) {
                                $item[$modelName]['name'] = $username['local_username'];
                            }
                            $data[$key] = $item;
                        }
                    }
                }
            }
        }
    
        return parent::afterFind($data);
    }
    
    public function getSubscribedGroups() {
        if(!$this->id) {
            return false;
        }
        
        $data = false;
        $data = $this->find('first', array(
            'contain' => array('SubscribedGroup')
        ));
        if($data) {
            $data = $data['SubscribedGroup'];
        }

        return $data;
    }
    
    public function getSubscribedNetworks() {
        if(!$this->id) {
            return false;
        }
        
        $data = false;
        $networks = $this->find('first', array(
            'contain' => array('SubscribedNetwork', 'Network')
        ));
        if($networks) {
            $data = $networks['SubscribedNetwork'];
            $data[] = $networks['Network'];
        }
        
        return $data;
    }
    
    /**
     * Is used, when an account is closed, so that the username
     * remains, but every other personal data is deleted.
     */
    public function block($identity_id = null) {
        if($identity_id === null) {
            $identity_id = $this->id;
        }
        
        $data = array('id'                => $identity_id,
                      'password'          => '',
                      'hash'              => '#deleted#',
                      'email'             => '',
                      'firstname'         => '',
                      'lastname'          => '',
                      'photo'             => '',
                      'about'             => '',
                      'address'           => '',
                      'address_shown'     => '',
                      'latitude'          => 0,
                      'longitude'         => 0,
                      'birthday'          => '1900-01-01',
                      'sex'               => 0,
                      'frontpage_updates' => 0,
                      'generic_feed'      => 0,
                      'security_token'    => '');
        $saveable = array_keys($data);
        $saveable[] = 'modified';
        $this->save($data, false, $saveable);
    }
    
    public function check($data) {
        $splitted = $this->splitUsername($data['Identity']['username']);
        $username = $splitted['username'];
        $this->contain();
        return $this->find(array('Identity.hash' => '',
                                 'Identity.username' => $username, 
                                 'Identity.password' => md5($data['Identity']['password'])));
    }
    
    public function createGuestIdentity($openID) {
    	App::import('Vendor', 'UrlUtil');
    	$data = array(
    				'network_id' => 0,
    				'username' => trim(UrlUtil::removeHttpAndHttps($openID), '/'),
    				'openid' => $openID
    			);

    	$this->save($data, false);
    	
    	return $this->find('first', array(
    	    'contain' => false,
    	    'conditions' => array('id' => $this->id)
    	));
    }
    
    public function getIdentityByOpenIDResponse($openIDResponse) {
    	$this->contain();
    	$identity = $this->find('first', array(
    	    'contain' => false,
    	    'conditions' => array(
    	        'Identity.hash'   => '', 
    	        'Identity.openid' => $openIDResponse->identity_url
    	    )
    	));
    	
    	if ($identity) {
    		$openIDIdentity = $openIDResponse->message->getArg('http://openid.net/signon/1.0', 'identity');
    		$openIDServerUrl = $openIDResponse->endpoint->server_url;
    		
    		# The OpenID identity resp. the server url can change if the
    		# user's OpenID is delegated. If that is the case we update those settings so 
    		# we can delegate the NoseRub OpenID to the correct OpenID
    		if ($identity['Identity']['openid_identity'] != $openIDIdentity || 
    		    $identity['Identity']['openid_server_url'] != $openIDServerUrl) {

    			$identity['Identity']['openid_identity'] = $openIDIdentity;
    		    $identity['Identity']['openid_server_url'] = $openIDServerUrl;
    		    $this->save($identity, false);
    		}
    		
    		return $identity;
    	}
    	
    	return false;
    }
    
    public function isCorrectSecurityToken($identity_id, $security_token) {
        if($identity_id && $security_token) {
            $this->id = $identity_id;
            $db_security_token = $this->field('security_token');
            return $db_security_token == $security_token;
        } else {
            return false;
        }
    }
    
    public function getIdentityByLocalUsername($localUsername) {
    	$splitted = $this->splitUsername($localUsername);
		$this->contain();
		$identity = $this->findByUsername($splitted['username']);
		
        return $identity;
    }
    
    /**
     * Returns mutual contacts of two identities, or false if there are no mutual contacts.
     */
    public function getMutualContacts($firstIdentityId, $secondIdentityId, $limit = null) {
    	$query = 'SELECT with_identity_id FROM contacts WHERE identity_id='.$firstIdentityId . ' AND with_identity_id IN (SELECT with_identity_id FROM contacts WHERE identity_id='.$secondIdentityId.')';
        $ids = $this->query($query);
        
    	if($ids) {
			$mutualContactsIds = join(',', Set::extract($ids, '{n}.contacts.with_identity_id'));

			$this->contain();
			$mutualContacts = $this->find('all', array('conditions' => array('Identity.id IN (' . $mutualContactsIds . ')'),
													   'order' => array('Identity.created DESC'),
													   'limit' => $limit));
			
			return $mutualContacts;
		}
			
		return false;
    }
    
    /**
     * Returns the newest identities.
     */
    public function getNewbies($limit = null) {
        $this->contain();

        $newbies = $this->find(
            'all', 
            array(
                'conditions' => array(
                    'network_id' => Configure::read('context.network.id'),
        			'frontpage_updates' => 1,
        			'hash' => '',
        			'username NOT LIKE "%@%"'
        		),
        		'order' => array('Identity.created DESC'),
        		'limit' => $limit
        	)
        );
        
        return $newbies;
    }
    
    /**
     * Opens the NoseRub page and parses the FOAF data
     * This is not a real FOAF-Paser, but rather a lame excuse...
     * Please also see /app/views/elements/foaf.ctp for another hack within
     * the FOAF data.
     *
     * @param string $url complete url of the NoseRub page that should be parsed  
     * @return array with information about the accounts and blogs from that page
     * @access 
     */
    public function parseNoseRubPage($url) {
        if(!$url) {
            return false;
        }

        App::import('Vendor', 'WebExtractor');
        $content = WebExtractor::fetchUrl($url);
        if(!$content) {
            return false;
        }

        $result = array(
            'accounts' => array(),
            'Identity' => array()
        );
        
        preg_match('/<foaf:Person rdf:nodeID="(.*)">/i', $content, $noserub_id);
        if(empty($noserub_id)) {
            App::import('Vendor', 'microformat'.DS.'hcard');
            App::import('Vendor', 'microformat'.DS.'xfn');
            $hcard_obj = new hcard;
        	$hcards = $hcard_obj->getByURL($url);
        	$hcard = $this->getOwner($hcards, $url);
        	if($hcard) {
        	    if(!isset($hcard['n']) && isset($hcard['fn'])) {
        	        $result['Identity']['firstname']     = '';
                    $result['Identity']['lastname']      = $hcard['fn'];
        	    } else if(isset($hcard['n'])) {
                    $result['Identity']['firstname']     = $hcard['n']['given-name'];
                    $result['Identity']['lastname']      = $hcard['n']['family-name'];
                    $result['Identity']['gender']        = 0;
                }

                # because of bug in hKit for relative URLs
                $photo  = isset($hcard['photo']) ? $hcard['photo'] : '';
                if($photo) {
                    $photo = str_replace(':///', '', $photo);
                    if(strpos($photo, 'ttp://') === false) {
                        $photo = $url . '/' . $photo;
                    }
                }
                $result['Identity']['photo'] = $photo;                
                $result['Identity']['address_shown'] = '';
                $result['Identity']['latitude']      = 0;
                $result['Identity']['longitude']     = 0;
        	} else if(strpos($url, 'friendfeed.com/') > 0) {
                # Fix for friendfeed, as they don't support hCard
                $info_content_start = strpos($content, '<table class="feedprofile">');
                $info_content_end   = strpos($content, '</a></div></td>');
                $info_content = substr($content, $info_content_start, $info_content_end-$info_content_start);
                
                if(preg_match('/<img .*src="http:\/\/i\.friendfeed\..*\/p-(.*)"/iU', $info_content, $matches)) {
                    $result['Identity']['photo'] = 'http://i.friendfeed.com/p-' . $matches[1];
                }
                if(preg_match('/<span.*>(.*)<\/span>/iU', $info_content, $matches)) {
                    $name = split(' ', $matches[1]);
                    if(count($name) == 1) {
                        $result['Identity']['lastname'] = $name;
                    } else {
                        $result['Identity']['lastname'] = $name[count($name) - 1];
                        unset($name[count($name) - 1]);
                        $result['Identity']['firstname'] = join(' ', $name);
                    }
                }
            }
        	$xfn = new xfn;
        	$xfn = $xfn->getByUrl($url);
        	$splitted = $this->splitUsername($url, false);
        	foreach($xfn as $xfn_url) {
        	    $serviceData = $this->Account->Service->detectService($xfn_url);
        	    if(!$serviceData) {
        	        $serviceData = array(
    		            'service_id' => 8,
    		            'username'   => $xfn_url
    		        );
        	    }
        	    $account = $this->Account->Service->getInfoFromService(
        	        $splitted['username'], 
        	        $serviceData['service_id'], 
        	        $serviceData['username']
        	    );
        	    if(!$account) {
        	        # as we don't know the service type id, we set the id to 3 for Text/Blog 
        			$account = $this->Account->Service->getInfoFromFeed($splitted['username'], 3, $xfn_url);
        		}
        		if($account) {	
                    $result['accounts'][] = $account; 
                }
        	}
        } else {
            preg_match('/<foaf:firstname>(.*)<\/foaf:firstname>/i', $content, $firstname);
            preg_match('/<foaf:surname>(.*)<\/foaf:surname>/i', $content, $lastname);
            preg_match('/<foaf:gender>(.*)<\/foaf:gender>/i', $content, $gender);
            preg_match('/<foaf:img>(.*)<\/foaf:img>/i', $content, $photo);
            preg_match('/<foaf:address>(.*)<\/foaf:address>/i', $content, $address);
            preg_match('/<geo:lat>(.*)<\/geo:lat>/i', $content, $latitude);
            preg_match('/<geo:long>(.*)<\/geo:long>/i', $content, $longitude);
            preg_match('/<foaf:about><!\[CDATA\[(.*)\]\]><\/foaf:about>/ims', $content, $about);
            preg_match_all('/<foaf:OnlineAccount rdf:about="(.*)".*\/>/i', $content, $accounts);
            preg_match_all('/<foaf:accountServiceHomepage rdf:resource="(.*)".*\/>/iU', $content, $services);
            preg_match_all('/<foaf:accountName>(.*)<\/foaf:accountName>/i', $content, $usernames);
               
            $result['Identity']['firstname'] = $firstname ? $firstname[1] : '';
            $result['Identity']['lastname']  = $lastname  ? $lastname[1]  : '';
            if($gender) {
                switch($gender[1]) {
                    case 'female': $result['Identity']['sex'] = 1; break;
                    case 'male'  : $result['Identity']['sex'] = 2; break;
                    default      : $result['Identity']['sex'] = 0;
                }
            } else {
                $result['identity']['sex'] = 0;
            }
            $result['Identity']['photo']         = $photo     ? $photo[1]     : '';
            $result['Identity']['address_shown'] = $address   ? $address[1]   : '';
            $result['Identity']['latitude']      = $latitude  ? $latitude[1]  : 0;
            $result['Identity']['longitude']     = $longitude ? $longitude[1] : 0;
            
            if(is_array($accounts)) {
                # gather all account data
                foreach($accounts[1] as $idx => $account_url) {
                    $account = array();

                    if(strpos($services[1][$idx], 'NoseRubServiceType:') === 0) {
                        # this is service_id 8 => any RSS-Feed
                        $account['feed_url']    =  isset($usernames[1][$idx]) ? $usernames[1][$idx] : '';
                        $account['account_url'] =  $account_url;
                        $account['service_id']  = 8;
                        $splitted = split(':', $services[1][$idx]);
                        $account['service_type_id'] = $splitted[1];
                        $account['username'] = 'RSS-Feed';
                    } else {
                        $account['account_url'] = $account_url;
                        $account['service_url'] = isset($services[1][$idx])  ? $services[1][$idx]  : '';
                        $account['username']    = isset($usernames[1][$idx]) ? $usernames[1][$idx] : '';
                        $info = $this->Account->Service->getInfo($account['service_url'], $account['username']);
                        $account['service_id']      = isset($info['service_id'])      ? $info['service_id']      : 0;
                        $account['service_type_id'] = isset($info['service_type_id']) ? $info['service_type_id'] : 0;
                        $account['feed_url']        = isset($info['feed_url'])        ? $info['feed_url']        : '';
                    }

                    $result['accounts'][] = $account; 
                }
            }
        }
        
        return $result;
    }
    
    private function getOwner($hcards, $url) {
        if(count($hcards) > 1) {
            foreach($hcards as $hcard) {
                if(isset($hcard['uid']) && isset($hcard['url']) && 
                   $hcard['uid'] && $hcard['url']) {
                    if(in_array($url, $hcard['url']) || in_array($url . '/', $hcard['url'])) {
                        return $hcard;
                    }
                }
            }
        }
        
        # just take the first one...
        return isset($hcards[0]) ? $hcards[0] : false;
    }
    
    public function register($data) {
        $isAccountWithOpenID = isset($data['Identity']['openid']);
    	
    	# transform it to a real username
        $splitted = $this->splitUsername($data['Identity']['username']);
        if($splitted['local'] == 0) {
            # registering here for an other server
            # is not possible
            return false;
        }
        $this->create();
        $data['Identity']['network_id']       = Configure::read('context.network.id');
        $data['Identity']['overview_filters'] = 'photo,video,link,text,micropublish,event,document,location,noserub';
        
        if (!$isAccountWithOpenID) { 
        	$data['Identity']['password'] = md5($data['Identity']['passwd']);
        }
        
        $data['Identity']['username'] = $splitted['username'];
        $data['Identity']['hash'] = md5(time().$data['Identity']['username']);
      
        // XXX for some reason I have to set this variable, otherwise not all validations work
        $this->data = $data;
        if(!$this->save($data, true, $this->getSaveableFields($isAccountWithOpenID))) {
            return false;
        }

        return true;
    }
    
    /**
     * removes http://, https:// and www. from url
     */
    public function removeHttpWww($url) {
    	App::import('Vendor', 'UrlUtil');
    	$url = UrlUtil::removeHttpAndHttps($url);
        if(stripos($url, 'www.') === 0) {
            $url = substr($url, 4);
        }
        
        return $url;
    }
    
    /**
     * extract single information out of a username
     * a username may have the following occurences:
     * (1) noserub.com/dirk.olbertz
     * (2) dirk.olbertz (this would be a local one)
     * (3) poolie@dirk.olbertz (this is a private, local one)
     * (4) noserub.com/poolie@dirk.olbertz (a private, local one)
     * (5) (1) and (4) with http:// or https://
     */
    public function splitUsername($username, $assume_local = true) {
        # first, remove http://, https:// and www.
        $username = $this->removeHttpWww($username);
        
        # remove trailing slashes
        $username = trim($username, '/');
        
        # now, we can extract the local username and the server
        $splitted = split('/', $username);
        if(!$splitted) {
            # something strange happened
            return false;
        }
        
        if(count($splitted) == 1 && $assume_local) {
            # just a username was given. so we assume it should
            # be for this server
            $local_username = $splitted[0];
            $servername = FULL_BASE_URL;
            $servername = $this->removeHttpWww($servername);
            $username =  $servername . Router::url('/') . $local_username;
        } else {
            $servername = $splitted[0];
            $local_username = array_pop($splitted);
            $username = join('/', $splitted);
            if($username) {
                $username .= '/';
            } 
            $username .= $local_username;
        }

        # test, wether we have a namespace here, or not
        $local_username_namespace = split('@', $local_username);
        
        # test, if this is a local contact, or not
        $server_name = FULL_BASE_URL . Router::url('/');
        $server_name = $this->removeHttpWww($server_name);
        $local = stripos($username, $server_name) === 0;
        $result = array(
            'username'        => $username,
            'local_username'  => $local_username,
            'single_username' => isset($local_username_namespace[0]) ? $local_username_namespace[0] : $local_username,
            'namespace'       => isset($local_username_namespace[1]) ? $local_username_namespace[1] : '',
            'servername'      => $servername,
            'local'           => $local ? 1 : 0
        );
        
        return $result;
    }
    
    /**
     * Updates the security token for given $identity_id
     */
    public function updateSecurityToken($identity_id) {
        if($identity_id) {
            $this->id = $identity_id;
            $security_token = md5($identity_id.time());
            $this->saveField('security_token', $security_token);
            
            return $security_token;
        }
        
        return false;
    }
    
    /**
     * sync that identity with data from username (url)
     */
    public function sync($identity_id, $username) {
        $this->log('sync('.$identity_id.', '.$username.')', LOG_DEBUG);
        # get the data from the remote server. try http:// and
        # https://
        $protocols = array('http://', 'https://');
        foreach($protocols as $protocol) {
            $data = $this->parseNoseRubPage($protocol . $username);
            if($data) {
                # we had success, so we don't need to try
                # the remaining protocol(s)
                break;
            }
        }
        
        if(!$data) {
            # no data was found!
            return false;
        }
        
        # update all accounts for that identity
        # @todo: not so nice to update another model here
        $this->Account->update($identity_id, $data['accounts']);

        # update 'last_sync' field and also identity information
        $this->id = $identity_id;
        $data['Identity']['last_sync'] = date('Y-m-d H:i:s');
        $this->save($data['Identity']);
        
        return true;
    }
    
    public function verify($hash) {
        # check, if there is a username with that hash
        $this->contain();
        $identity = $this->find(array('Identity.hash' => $hash));
        if($hash && $identity) {
            # update the identity
            $this->id = $identity['Identity']['id'];
            return $this->saveField('hash', '');
        } else {
            return false;
        }
    }
    
    public function export() {
        $this->recursive = 0;
        $data = $this->read();
        $vcard = $data['Identity'];  
        $vcard['username'] = $vcard['local_username'];      
        $to_remove = array(
            'id', 'network_id', 'password', 'openid', 'openid_identity', 
            'openid_server_url', 'email', 'last_location_id', 
            'api_hash', 'api_active', 'hash', 'security_token',
            'last_generic_feed_upload', 'last_sync', 'created', 'modified',
            'local_username', 'single_username', 'namespace',
            'local', 'servername', 'name', 'overview_filters',
            'frontpage_updates', 'allow_emails'
        );
        foreach($to_remove as $key) {
            unset($vcard[$key]);
        }
        $server = array(
            'base_url' => trim($this->removeHttpWww(FULL_BASE_URL . Router::url('/')), '/'),
            'version'  => 1
        );
        
        # make the photo hash an external url
        if($vcard['photo']) {
            $vcard['photo'] = $this->getBaseUrlForAvatars() . $vcard['photo'] . '.jpg';
        }
    
        return array(
            'server'    => $server,
            'vcard'     => $vcard,
            'contacts'  => $this->Contact->export($this->id),
            'accounts'  => $this->Account->export($this->id),
            'locations' => $this->Location->export($this->id)
        );
    }
    
    public function import($data) {
        if(!$this->exists()) {
            return false;
        }
        
        if(!isset($data['server']['version']) || $data['server']['version'] != 1) {
            return false;
        }

        $vcard = isset($data['vcard']) ? $data['vcard'] : null;
        if(!$vcard) {
            return false;
        }
        
        $username = isset($vcard['username']) ? $vcard['username'] : '';
        if(!$username) {
            return false;
        }
        
        # get current identity, so we can check what
        # to update
        $this->contain();
        $identity = $this->read();
        $identity = $identity['Identity'];
        $saveable = array();
        if(!$identity['firstname'])     { $saveable[] = 'firstname'; }
        if(!$identity['lastname'])      { $saveable[] = 'lastname'; }
        if(!$identity['about'])         { $saveable[] = 'about'; }
        if(!$identity['address'])       { $saveable = array_merge($saveable, array('address', 'latitude', 'longitude'));}
        if(!$identity['address_shown']) { $saveable[] = 'address_shown'; }
        if(!$identity['birthday'])      { $saveable[] = 'birthday'; }
        if(!$identity['sex'])           { $saveable[] = 'sex'; }
        $vcard['openid'] = null;
        $saveable[] = 'openid';
        if(!$this->save($vcard, false, $saveable)) {
            $this->log('error on saving vcard');
            return false;
        }
        if(!$identity['photo']) {
            if($vcard['photo']) {
                $this->uploadPhotoByUrl($vcard['photo']);
            }
        }
        
        $contacts = isset($data['contacts']) ? $data['contacts'] : array();
        if(!$this->Contact->import($this->id, $contacts)) {
            $this->log('error on importing contacts');
            return false;
        }
        
        $accounts = isset($data['contacts']) ? $data['accounts'] : array();
        if(!$this->Account->import($this->id, $accounts)) {
            $this->log('error on importing accounts');
            return false;
        }
        
        $locations = isset($data['locations']) ? $data['locations'] : array();
        if(!$this->Location->import($this->id, $locations)) {
            $this->log('error on importing locations');
            return false;
        }
        
        return true;
    }
    
    public function readImport($filename) {
        $content = @file_get_contents($filename);
        if(!$content) {
            return false;
        }
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        $data = Zend_Json::decode($content);

        if(!is_array($data) || 
           !isset($data['server']['base_url']) || 
           !isset($data['vcard']['username'])) {
            return false;
        }
        
        return $data;
    }
    
    /**
     * tries to find an identity, given
     * by that username. if it is not found,
     * we create this identity.
     * this is used for Comment::sync()
     *
     * ATTENTION! getId() was not available, as it is already used
     *
     * @param string $username
     * 
     * @return array
     */
    public function getIdForUsername($username) {
        $this->contain();
        $identity = $this->find(
            'first',
            array(
                'conditions' => array(
                    'Identity.username' => $username
                ),
                'fields' => 'Identity.id'
            )
        );
        
        if(!$identity) {
            $data = array(
                'username'   => $username,
                'network_id' => 0
            );
            $this->create();
            $this->save($data);
            
            return $this->id;
        } else {
            return $identity['Identity']['id'];
        }
    }
    
    private function uploadPhoto($local_filename) {
        if(!$this->exists()) {
            return false;
        }
        
        $imageinfo = getimagesize($local_filename);
        $picture = $this->createImage($imageinfo[2], $local_filename);
        
        if($picture) {
            $filename = $this->field('photo');
            # check, if this isn't a gravatar image
            if(strpos($filename, 'http') === 0) {
                $filename = '';
            }
            
            if(!$filename) {
                $filename = $this->generateUniqueFilenameForPhoto($this->id . $local_filename);
                $this->saveField('photo', $filename);
            }
            
            App::import('Vendor', 'ImageResizer');
            $originalSize = new ImageSize($imageinfo[0], $imageinfo[1]);
            ImageResizer::resizeAndSaveJPEG($picture, $originalSize, new ImageSize(150, 150), AVATAR_DIR . $filename . '.jpg');
            ImageResizer::resizeAndSaveJPEG($picture, $originalSize, new ImageSize(96, 96), AVATAR_DIR . $filename . '-medium.jpg');
            ImageResizer::resizeAndSaveJPEG($picture, $originalSize, new ImageSize(35, 35), AVATAR_DIR . $filename . '-small.jpg');
            
            return $filename;
        }
        
        return false;
    }
    
    private function createImage($imageType, $localFilename) {
    	switch($imageType) {
            case IMAGETYPE_GIF:
                $picture = imageCreateFromGIF($localFilename);
                break;
                
            case IMAGETYPE_JPEG:
                $picture = imageCreateFromJPEG($localFilename);
                break;
                
            case IMAGETYPE_PNG:
                $picture = imageCreateFromPNG($localFilename);
                break;
                
            default:
                $picture = null;
        }
        
        return $picture;
    }
    
    private function generateUniqueFilenameForPhoto($seed) {
		$filename = '';
		
		while($filename == '') {
			$filename = md5($seed);
			if(file_exists(AVATAR_DIR . $filename . '.jpg')) {
				$filename = '';
				$seed = md5($seed . time());
			}
		}
		
		return $filename;
    }
    
    public function uploadPhotoByForm($upload_form) {
		return $this->uploadPhoto($upload_form['tmp_name']);
    }
       
    public function uploadPhotoByUrl($url) {
        # get the file first
        App::import('Vendor', 'WebExtractor');
        $content = WebExtractor::fetchUrl($url);
        if($content) {
            $filename = AVATAR_DIR . $this->id . '.tmp';
            file_put_contents($filename, $content);
            $this->saveField('photo', '');
            $result = $this->uploadPhoto($filename);
            @unlink($filename);
            return $result;
        } else {
            return false;
        }
    }
    
    private function getSaveableFields($isAccountWithOpenID) {
    	$saveable = array(
    	    'network_id', 'username', 'email', 'hash', 'frontpage_updates', 
    	    'allow_emails', 'overview_filters', 'notify_contact',
    	    'notify_comment', 'notify_favorite', 'created', 'modified'
    	);
    	
    	if ($isAccountWithOpenID) {
    		$saveable[] = 'openid';
    		$saveable[] = 'openid_identity';
    		$saveable[] = 'openid_server_url';
    	} else {
    		$saveable[] = 'password';
    	}
    	
    	return $saveable;
    }
    
    /**
     * @param $identityKey Either 'Identity' or 'WithIdentity'
     */
    public function getPhotoUrl($data, $identityKey = 'Identity', $smallSize = false) {
        if($data[$identityKey]['photo']) {
            if($this->startsWithHttp($data[$identityKey]['photo'])) {
                   # contains a complete path, eg. from not local identities
                   $profile_photo = $data[$identityKey]['photo'];
               } else {
                   $profile_photo = $this->getBaseUrlForAvatars() . $data[$identityKey]['photo'] . '.jpg';
               }
        } else {
            App::import('Vendor', 'sex');
            if ($smallSize) {
            	$profile_photo = Sex::getSmallImageUrl($data[$identityKey]['sex']);
            } else {
        		$profile_photo = Sex::getImageUrl($data[$identityKey]['sex']);
            }
        }
        
        return $profile_photo;
    }
    
    public function getBaseUrlForAvatars() {
    	$url = '';
    	
    	if(Configure::read('NoseRub.use_cdn')) {
            $url = 'http://s3.amazonaws.com/' . Configure::read('NoseRub.cdn_s3_bucket') . '/avatars/';
        } else {
            $url = Router::url('/static/avatars/', true);
        }
        
        return $url;
    }
    
    /**
     * returns the last active identities
     */
    public function getLastActive($limit, $with_restricted = false) {
        $this->Entry->contain();
        $fields = array('DISTINCT Entry.identity_id');
        $conditions = array();
        if(!$with_restricted) {
            $conditions['restricted'] = 0;
        }
        $entries = $this->Entry->find(
            'all',
            array(
                'fields'     => $fields,
                'conditions' => $conditions,
                'order'      => 'Entry.published_on DESC',
                'limit'      => $limit
            )
        );
        $identity_ids = Set::extract($entries, '{n}.Entry.identity_id');

        $this->contain();
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'id' => $identity_ids
                ),
                'limit' => $limit
            )
        );
    }
        
    /**
     * go through all $model to load the identity
     *
     * @param string $model eg. FavoritedBy, or Comment
     * @param arary $data
     *
     * @return array
     */
    public function addIdentity($model, $data) { 
        if(!isset($data[$model])) {
            return $data;
        }       
        
        foreach($data[$model] as $idx => $item) {
            $this->contain();
            $this->id = $item['identity_id'];
            $identity = $this->read();
            $data[$model][$idx]['Identity'] = $identity['Identity'];
        }
        
        return $data;
    }
    
    /**
     * tests, if there is an identity available in this network to
     * which someone could log in. this is needed to decide wether
     * the admin route can be access without being logged in as
     * an identity.
     */
    public function isIdentityAvailableForLogin() {
        return $this->find('count', array(
            'contain' => false,
            'conditions' => array(
                'network_id' => Configure::read('context.network.id'),
                'hash' => '',
                'username <>' => '',
                'password <>' => ''
            )
        ));
    }
    
    private function startsWithHttp($string) {
    	return (strpos($string, 'http://') === 0 ||
                strpos($string, 'https://') === 0);
    }
}