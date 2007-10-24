<?php
/* SVN FILE: $Id:$ */
 
class Identity extends AppModel {
    var $hasMany = array('Contact', 'Account', 'OpenidSite');
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', NOSERUB_VALID_USERNAME)),
                                'unique'   => array('rule' => 'validateUniqueUsername'),
                                'required' => VALID_NOT_EMPTY),
            'email'    => array('mail'     => VALID_EMAIL,
                                'required' => VALID_NOT_EMPTY),
            'passwd'   => array('rule' => array('minLength', 6)),
            'passwd2'  => array('rule' => 'validatePasswd2'),
    		'openid'   => array('rule' => 'validateUniqueOpenID')
        );
    
    function validatePasswd2($value, $params = array()) {
        if($this->data['Identity']['passwd'] !==$value) {
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
    function validateUniqueUsername($value, $params = array()) {
        $value = strtolower($value);
        $split_username = $this->splitUsername($value);
        if(in_array($split_username['username'], split(',', NOSERUB_RESERVED_USERNAMES))) {
            return false;
        }

        $this->recursive = 0;
        $this->expects('Identity');
        if($this->findCount(array('Identity.username' => $value)) > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    function validateUniqueOpenID($value, $params = array()) {
    	if ($this->findCount(array('Identity.openid' => $value, 'Identity.hash' => '<> #deleted#')) > 0) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function afterFind($data) {
        # moved from Identity Model to here, so that all the associated
        # afterFinds could be catched, too
        if(is_array($data)) {
            if(isset($data['username'])) {
                $username = Identity::splitUsername($data['username']);
                $data['local_username'] = $username['local_username'];
                $data['username']       = $username['username'];
                $data['namespace']      = $username['namespace'];
                $data['local']          = $username['local'];
                $data['name']           = trim($data['firstname'] . ' ' . $data['lastname']);
            } else {
                foreach($data as $key => $item) {
                    $checkModels = array('WithIdentity', 'Identity');
                    foreach($checkModels as $modelName) {
                        if(isset($item[$modelName]['username'])) {
                            $username = Identity::splitUsername($item[$modelName]['username']);
                            $item[$modelName]['local_username'] = $username['local_username'];
                            $item[$modelName]['username']       = $username['username'];
                            $item[$modelName]['namespace']      = $username['namespace'];
                            $item[$modelName]['local']          = $username['local'];
                            $item[$modelName]['name']           = trim($item[$modelName]['firstname'] . ' ' . $item[$modelName]['lastname']);
                            $data[$key] = $item;
                        }
                    }
                }
            }
        }
    
        return $data;
    }
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function check($data) {
        $splitted = $this->splitUsername($data['Identity']['username']);
        $username = $splitted['username'];
        $this->recursive = 0;
        $this->expects('Identity');
        return $this->find(array('Identity.hash' => '',
                                 'Identity.username = "'. $username .'"', 
                                 'Identity.password' => md5($data['Identity']['password'])));
    }
    
    public function checkOpenID($openid) {
    	$this->recursive = 0;
    	$this->expects('Identity');
    	return $this->find(array('Identity.hash' => '', 'Identity.openid' => $openid));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
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
        
        if(!$this->save($data, true, $this->getSaveableFields($isAccountWithOpenID))) {
            return false;
        }

        $msg = $this->prepareVerificationMessage($data['Identity']['hash']);
        $this->sendVerificationMail($data['Identity']['email'], $msg);

        return true;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function verify($hash) {
        # check, if there is a username with that hash
        $this->recursive = 0;
        $this->expects = array('Identity');
        $identity = $this->find(array('Identity.hash' => $hash));
        if($hash && $identity) {
            # update the identity
            $this->id = $identity['Identity']['id'];
            return $this->saveField('hash', '');
        } else {
            return false;
        }
    }
    
    /**
     * Is used, when an account is closed, so that the username
     * remains, but every other personal data is deleted.
     *
     * @param  
     * @return 
     * @access 
     */
    function block($identity_id = null) {
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
                      'frontpage_updates' => 0);
        $saveable = array_keys($data);
        $saveable[] = 'modified';
        $this->save($data, false, $saveable);
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
    function splitUsername($username) {
        # first, remove http://, https:// and www.
        $username = str_ireplace('http://', '', $username);
        $username = str_ireplace('https://', '', $username);
        if(stripos($username, 'www.') === 0) {
            $username = str_ireplace('www.', '', $username);
        }
        
        # remove trailing slashes
        $username = trim($username, '/');
        
        # now, we can extract the local username and the server
        $splitted = split('/', $username);
        if(!$splitted) {
            # something strange happened
            return false;
        }
        
        if(count($splitted) == 1) {
            # just a username was given. so we assume it should
            # be for this server
            $local_username = $splitted[0];
            $servername = FULL_BASE_URL;
            $servername = str_ireplace('http://', '', $servername);
            $servername = str_ireplace('https://', '', $servername);
            $username =  $servername . Router::url('/') . $local_username;
        } else {
            $servername = $splitted[0];
            $local_username = array_pop($splitted);
            $username = join('/', $splitted) . '/' . $local_username;
        }

        # test, wether we have a namespace here, or not
        $local_username_namespace = split('@', $local_username);
        
        # test, if this is a local contact, or not
        $server_name = str_ireplace('http://', '', FULL_BASE_URL . Router::url('/'));
        $server_name = str_ireplace('https://', '', $server_name);
        if(stripos($server_name, 'www.') === 0) {
            $server_name = str_ireplace('www.', '', $server_name);
        }
        $local = stripos($username, $server_name) === 0;
        $result = array('username'        => $username,
                        'local_username'  => $local_username,
                        'single_username' => isset($local_username_namespace[0]) ? $local_username_namespace[0] : $local_username,
                        'namespace'       => isset($local_username_namespace[1]) ? $local_username_namespace[1] : '',
                        'servername'      => $servername,
                        'local'           => $local);
        
        return $result;
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
    function sanitizeUsername($username) {
        $username = str_replace('ä', 'ae', $username);
        $username = str_replace('ö', 'oe', $username);
        $username = str_replace('ü', 'ue', $username);
        $username = str_replace('ß', 'ss', $username);
        $username = str_replace('Ä', 'Ae', $username);
        $username = str_replace('Ö', 'Oe', $username);
        $username = str_replace('Ü', 'Ue', $username);
        
        $username = preg_replace('/[^\w\s.-]/', null, $username);
        return $username;
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
    function parseNoseRubPage($url) {
        if(!$url) {
            return false;
        }

        # "@" to avoid notices and warnings on not supported
        # protocol, e.g. https
        $content = @file_get_contents($url);
        if(!$content) {
            return false;
        }

        preg_match('/<foaf:Person rdf:nodeID="(.*)">/i', $content, $noserub_id);
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
        
        
        #echo 'NOSERUB-ID<pre>'; print_r($noserub_id); echo '</pre>';
        #echo 'FIRSTNAME<pre>'; print_r($firstname); echo '</pre>';
        #echo 'LASTNAME<pre>'; print_r($lastname); echo '</pre>';
        #echo 'GENDER<pre>'; print_r($gender); echo '</pre>';
        #echo 'PHOTO<pre>'; print_r($photo); echo '</pre>';
        #echo 'ADDRESS<pre>'; print_r($address); echo '</pre>';
        #echo 'LATITUDE<pre>'; print_r($latitude); echo '</pre>';
        #echo 'LONGITUDE<pre>'; print_r($longitude); echo '</pre>';
        #echo 'ABOUT<pre>'; print_r($about); echo '</pre>';
        #echo 'ACCOUNTS<pre>'; print_r($accounts); echo '</pre>';
        #echo 'SERVICES<pre>'; print_r($services); echo '</pre>';
        #echo 'USERNAMES<pre>'; print_r($usernames); echo '</pre>';
        
        if(empty($noserub_id)) {
            return false;
        }
        
        $result = array('accounts' => array(),
                        'Identity' => array());
        
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
        $result['Identity']['about']         = $about     ? html_entity_decode($about[1], ENT_COMPAT, 'UTF-8') : '';
        
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
        } else {
            return false;
        }
        
        return $result;
    }
    
    /**
     * sync that identity with data from username (url)
     *
     * @param  
     * @return 
     * @access 
     */
    function sync($identity_id, $username) {
        $this->log('sync('.$identity_id.', '.$username.')', LOG_DEBUG);
        # get the data from the remote server. try https:// and
        # http://
        $protocols = array('https://', 'http://');
        foreach($protocols as $protocol) {
            $data = $this->parseNoseRubPage($protocol . $username);
            if($data) {
                # we had success, so we don't need to try
                # the remaining protocol(s)
                continue;
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
    
    private function getSaveableFields($isAccountWithOpenID) {
    	$saveable = array('is_local', 'username', 'email', 'hash', 'frontpage_updates', 'created', 'modified');
    	
    	if ($isAccountWithOpenID) {
    		$saveable[] = 'openid';
    		$saveable[] = 'openid_server_url';
    	} else {
    		$saveable[] = 'password';
    	}
    	
    	return $saveable;
    }
    
    private function prepareVerificationMessage($hash) {
    	$msg  = 'Welcome to NoseRub!' . "\n\n";
        $msg .= 'Please click here to verify your email address:' ."\n";
        $msg .= FULL_BASE_URL . Router::url('/') . 'pages/verify/' . $hash . '/' . "\n\n";
        $msg .= 'If you do not click on this link, the account will automatically be deleted after 14 days.' . "\n\n";
        $msg .= 'Thanks!';
        
        return $msg;
    }
    
    private function sendVerificationMail($email, $msg) {
        if(!mail($email, 'Your NoseRub registration', $msg, 'From: ' . NOSERUB_EMAIL_FROM)) {
            $this->log('verify mail could not be sent: '.$email);
        } else {
            $this->log('verify mail sent to '.$email, LOG_DEBUG);
        }
    }
}
