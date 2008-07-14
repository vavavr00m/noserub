<?php
/* SVN FILE: $Id:$ */
 
class Identity extends AppModel {
    public $hasMany = array('Account', 'Contact', 'ContactType', 'Consumer', 'OpenidSite', 'Location', 'Activity', 'Syndication');
    public $belongsTo = array('Location' => array('className'  => 'Location',
                                               'foreignKey' => 'last_location_id'));
    
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
    	if ($this->hasAny(array('Identity.openid' => $value['openid'], 'Identity.hash' => '<> #deleted#'))) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    /**
     * validate, if the username is already taken
     *
     * @param  
     * @return 
     * @access 
     */
    public function validateUniqueUsername($value, $params = array()) {
        $value = strtolower($value['username']);
        $split_username = $this->splitUsername($value);
        if(in_array($split_username['username'], split(',', NOSERUB_RESERVED_USERNAMES))) {
            return false;
        }

        if($this->hasAny(array('Identity.username' => $value))) {
            return false;
        } else {
            return true;
        }
    }    
    
    /**
     * check, whether host of email address matches NOSERUB_REGISTRATION_RESTRICTED_HOSTS
     */
    public function validateRestrictedEmail($email, $params = array()) {
        if (!defined('NOSERUB_REGISTRATION_RESTRICTED_HOSTS') || 
            NOSERUB_REGISTRATION_RESTRICTED_HOSTS === false ||
            $email == '') {
            return true;
        }
        list($local, $host) = explode('@', $email['email']);
        return in_array($host, explode(' ', NOSERUB_REGISTRATION_RESTRICTED_HOSTS));
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
                            $data[$key] = $item;
                        }
                    }
                }
            }
        }
    
        return parent::afterFind($data);
    }
    
    /**
     * Is used, when an account is closed, so that the username
     * remains, but every other personal data is deleted.
     *
     * @param  
     * @return 
     * @access 
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
    
    public function checkOpenID($openIDResponse) {
    	$this->contain();
    	$identity = $this->find(array('Identity.hash' => '', 'Identity.openid' => $openIDResponse->identity_url));
    	
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
    	} else {
    		if (NOSERUB_ALLOW_REMOTE_LOGIN) {
    			# is it a remote user?
    			$username = $this->splitUsername($openIDResponse->identity_url);
    			$identity = $this->find(array('Identity.username' => $username['username']));
    		} else {
    			return false;
    		}
    	}
    	
    	if ($identity) {
    		return $identity;
    	}
    	
    	return false;
    }
    
    public function checkSecurityToken($identity_id, $security_token) {
        if($identity_id && $security_token) {
            $this->id = $identity_id;
            $db_security_token = $this->field('security_token');
            return $db_security_token == $security_token;
        } else {
            return false;
        }
    }

    /**
     * Returns the contacts of the specified identity, ordered by last activity.
     */
    public function getContacts($identityId, $limit = null) {
        $this->Contact->contain(array('WithIdentity', 'NoserubContactType'));
		$contacts = $this->Contact->findAllByIdentityId($identityId, null, 'WithIdentity.last_activity DESC', $limit);
		
		return $contacts;
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
			// TODO replace findAll with find('all')
			$mutualContacts = $this->findAll(array('Identity.id IN (' . $mutualContactsIds . ')'), null, 'Identity.last_activity DESC', $limit);
			
			return $mutualContacts;
		}
			
		return false;
    }
    
    /**
     * Returns the newest identities.
     */
    public function getNewbies($limit = null) {
        $this->contain();
        // TODO replace findAll with find('all')
        $newbies = $this->findAll(array('is_local' => 1, 
                                        'frontpage_updates' => 1,
                                        'hash' => '',
                                        'username NOT LIKE "%@%"'), null, 'Identity.created DESC', $limit);
        
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

        # "@" to avoid notices and warnings on not supported
        # protocol, e.g. https
        $content = @file_get_contents($url);
        if(!$content) {
            return false;
        }

        $result = array('accounts' => array(),
                        'Identity' => array());
        
        preg_match('/<foaf:Person rdf:nodeID="(.*)">/i', $content, $noserub_id);
        if(empty($noserub_id)) {
            App::import('Vendor', 'microformat'.DS.'hcard');
            App::import('Vendor', 'microformat'.DS.'xfn');
            $hcard_obj = new hcard;
        	$hcards = $hcard_obj->getByURL($url);
        	$hcard = $this->getOwner($hcards, $url);
        	if($hcard) {
        	    if(!isset($hvcard['n'])) {
        	        $result['Identity']['firstname']     = '';
                    $result['Identity']['lastname']      = $hcard['fn'];
        	        $this->log(print_r($hcard, 1));
        	    } else {
                    $result['Identity']['firstname']     = $hcard['n']['given-name'];
                    $result['Identity']['lastname']      = $hcard['n']['family-name'];
                    $result['Identity']['gender']        = 0;
                }
                
                # because of bug in hKit for relative URLs
                $photo  = isset($hcard['photo']) ? $hcard['photo'] : '';
                $photo = str_replace(':///', '', $photo);
                if(strpos($photo, 'ttp://') === false) {
                    $photo = $url . '/' . $photo;
                }
                $result['Identity']['photo'] = $photo;
                
                $result['Identity']['address_shown'] = '';
                $result['Identity']['latitude']      = 0;
                $result['Identity']['longitude']     = 0;
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
        $data['Identity']['is_local'] = 1;
        
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

        $msg = $this->prepareVerificationMessage($data['Identity']['hash']);
        $this->sendVerificationMail($data['Identity']['email'], $msg);

        return true;
    }

    /**
     * Sanitizes non-namespace containing usernames.
     * This is used eg. when adding new contacts from
     * flickr, where usernames can be '0909ds7@N01'.
     * There, the @ is not allowed, so I want to sanitize
     * them, before giving them to the user as selection
     * for using as a real contact username.
     *
     * @param  
     * @return 
     * @access 
     */
    public function sanitizeUsername($username) {
        $username = str_replace('ä', 'ae', $username);
        $username = str_replace('ö', 'oe', $username);
        $username = str_replace('ü', 'ue', $username);
        $username = str_replace('ß', 'ss', $username);
        $username = str_replace('Ä', 'Ae', $username);
        $username = str_replace('Ö', 'Oe', $username);
        $username = str_replace('Ü', 'Ue', $username);
        $username = str_replace(' ', '-',  $username);
        
        $username = preg_replace('/[^\w\s.-]/', null, $username);
        return $username;
    }
    
    /**
     * removes http://, https:// and www. from url
     */
    public function removeHttpWww($url) {
        $url = str_ireplace('http://', '', $url);
        $url = str_ireplace('https://', '', $url);
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
     *
     * @param  
     * @return 
     * @access 
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
        $result = array('username'        => $username,
                        'local_username'  => $local_username,
                        'single_username' => isset($local_username_namespace[0]) ? $local_username_namespace[0] : $local_username,
                        'namespace'       => isset($local_username_namespace[1]) ? $local_username_namespace[1] : '',
                        'servername'      => $servername,
                        'local'           => $local ? 1 : 0);
        
        return $result;
    }
    
    
    /**
     * if Identity.last_activity is before $datetime, it is updated in the
     * database.
     */
    public function updateLastActivity($datetime = null, $identity_id = null) {
        if($datetime === null) {
            # no datetime set, use now
            $datetime = date('Y-m-d H:i:s');
        } else {
            # make sure we have datetime and not only date or something like that
            $datetime = date('Y-m-d H:i:s', strtotime($datetime));
        
            # get now
            $now = date('Y-m-d H:i:s');
        
            if($datetime > $now) {
                # don't allow "last_activity" to be in the future
                return;
            }
        }
        
        # get the current value
        if($identity_id) {
            $this->id = $identity_id;
        }
        
        $last_activity = $this->field('Last_activity');
        if($last_activity < $datetime) {
            # set the new datetime
            $this->saveField('last_activity', $datetime);
        }
    }

    /**
     * Updates the security token for given $identity_id
     *
     * @param  
     * @return 
     * @access 
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
     *
     * @param  
     * @return 
     * @access 
     */
    public function sync($identity_id, $username) {
        $this->log('sync('.$identity_id.', '.$username.')', LOG_DEBUG);
        # get the data from the remote server. try http:// and
        # http2://
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
        $this->Account->replace($identity_id, $data['accounts']);

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
            'id', 'is_local', 'password', 'openid', 'openid_identity', 
            'openid_server_url', 'email', 'last_location_id', 
            'api_hash', 'api_active', 'hash', 'security_token',
            'last_activity', 'last_sync', 'created', 'modified',
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
            if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
                $static_base_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/avatars/';
            } else {
                $static_base_url = FULL_BASE_URL . Router::url('/static/avatars/');
            }
            $vcard['photo'] = $static_base_url . $vcard['photo'] . '.jpg';
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
        $zend_json = new Zend_Json();
        $zend_json->useBuiltinEncoderDecoder = true;
        
        $data = $zend_json->decode($content);

        if(!is_array($data) || 
           !isset($data['server']['base_url']) || 
           !isset($data['vcard']['username'])) {
            return false;
        }
        
        return $data;
    }
    
    private function uploadPhoto($local_filename) {
        if(!$this->exists()) {
            return false;
        }
        
        $imageinfo = getimagesize($local_filename);
        switch($imageinfo[2]) {
            case IMAGETYPE_GIF:
                $picture = imageCreateFromGIF($local_filename);
                break;
                
            case IMAGETYPE_JPEG:
                $picture = imageCreateFromJPEG($local_filename);
                break;
                
            case IMAGETYPE_PNG:
                $picture = imageCreateFromPNG($local_filename);
                break;
                
            default:
                $picture = null;
        }
        
        if($picture) {
            $filename = $this->field('photo');
            if(!$filename) {
                # get random name for new photo and make sure it is unqiue
                $filename = '';
                $seed = $this->id . $local_filename;
                while($filename == '') {
                    $filename = md5($seed);
                    if(file_exists(AVATAR_DIR . $filename . '.jpg')) {
                        $filename = '';
                        $seed = md5($seed . time());
                    }
                }
                $this->saveField('photo', $filename);
            }
            
            $original_width  = $imageinfo[0];
            $original_height = $imageinfo[1];

            $this->save_scaled($picture, $original_width, $original_height, 150, 150, AVATAR_DIR . $filename . '.jpg');
            $this->save_scaled($picture, $original_width, $original_height,  35,  35, AVATAR_DIR . $filename . '-small.jpg');
            
            return $filename;
        }
        
        return false;
    }
    
    public function uploadPhotoByForm($upload_form) {
       return $this->uploadPhoto($upload_form['tmp_name']);
    }
       
    public function uploadPhotoByUrl($url) {
        # get the file first
        $content = @file_get_contents($url);
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
    
    public function save_scaled($picture, $original_width, $original_height, $width, $height, $filename) {
        if($original_width==$width && $original_height==$height) {
            # original picture
            imagejpeg($picture, $filename, 100); # best quality
        } else {
            # resampling picture
            $resampled = imagecreatetruecolor($width, $height);
            imagecopyresampled($resampled, $picture, 0, 0, 0, 0, imagesx($resampled), imagesy($resampled), $original_width, $original_height);
            imagejpeg($resampled, $filename, 100); # best quality 
        }
    }
    
    private function getSaveableFields($isAccountWithOpenID) {
    	$saveable = array('is_local', 'username', 'email', 'hash', 'frontpage_updates', 'allow_emails', 'created', 'modified');
    	
    	if ($isAccountWithOpenID) {
    		$saveable[] = 'openid';
    		$saveable[] = 'openid_identity';
    		$saveable[] = 'openid_server_url';
    	} else {
    		$saveable[] = 'password';
    	}
    	
    	return $saveable;
    }
    
    private function prepareVerificationMessage($hash) {
    	$msg  = 'Welcome to ' . NOSERUB_APP_NAME . '!' . "\n\n";
        $msg .= 'Please click here to verify your email address:' ."\n";
        $msg .= FULL_BASE_URL . Router::url('/') . 'pages/verify/' . $hash . '/' . "\n\n";
        $msg .= 'If you do not click on this link, the account will automatically be deleted after 14 days.' . "\n\n";
        $msg .= 'Thanks!';
        
        return $msg;
    }
    
    private function sendVerificationMail($email, $msg) {
        if(!mail($email, 'Your ' . NOSERUB_APP_NAME . ' registration', $msg, 'From: ' . NOSERUB_EMAIL_FROM)) {
            $this->log('verify mail could not be sent: '.$email);
        } else {
            $this->log('verify mail sent to '.$email, LOG_DEBUG);
        }
    }
    
    public function getPhotoUrl($data) {
        $sex = array(
            'img' => array(
                0 => Router::url('/images/profile/avatar/noinfo.gif'),
                1 => Router::url('/images/profile/avatar/female.gif'),
                2 => Router::url('/images/profile/avatar/male.gif')
            )
        );
                                    
        if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
            $static_base_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/avatars/';
        } else {
            $static_base_url = FULL_BASE_URL . Router::url('/static/avatars/');
        }

        if($data['Identity']['photo']) {
            if(strpos($data['Identity']['photo'], 'http://') === 0 ||
               strpos($data['Identity']['photo'], 'https://') === 0) {
                   # contains a complete path, eg. from not local identities
                   $profile_photo = $data['Identity']['photo'];
               } else {
                   $profile_photo = $static_base_url . $data['Identity']['photo'] . '.jpg';
               }
        } else {
            $profile_photo = $sex['img'][$data['Identity']['sex']];
        }
        
        return $profile_photo;
    }
}
