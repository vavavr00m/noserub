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
            'passwd2'  => array('rule' => 'validatePasswd2')
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
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function register($data) {
        # transform it to a real username
        $splitted = $this->splitUsername($data['Identity']['username']);
        if($splitted['local'] == 0) {
            # registering here for an other server
            # is not possible
            return false;
        }
        $this->create();
        $data['Identity']['is_local'] = 1;
        $data['Identity']['password'] = md5($data['Identity']['passwd']);
        $data['Identity']['username'] = $splitted['username'];
        $data['Identity']['hash'] = md5(time().$data['Identity']['username']);
        $saveable = array('is_local', 'username', 'password', 'email', 'hash', 'frontpage_updates', 'created', 'modified');
        if(!$this->save($data, true, $saveable)) {
            return false;
        }
        
        # send out verification mail
        $msg  = 'Welcome to NoseRub!' . "\n\n";
        $msg .= 'Please click here to verify your email address:' ."\n";
        $msg .= FULL_BASE_URL . Router::url('/') . 'pages/verify/' . $data['Identity']['hash'] . '/' . "\n\n";
        $msg .= 'If you do not click on this link, the account will automatically be deleted after 14 days.' . "\n\n";
        $msg .= 'Thanks!';
        
        if(!mail($data['Identity']['email'], 'Your NoseRub registration', $msg, 'From: ' . NOSERUB_EMAIL_FROM)) {
            $this->log('verify mail could not be sent: '.$data['Identity']['email']);
        } else {
            $this->log('verify mail sent to '.$data['Identity']['email'], LOG_DEBUG);
        }
        
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
                      'address'           => '',
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
            $username = FULL_BASE_URL . Router::url('/') . $local_username;
            $username = str_ireplace('http://', '', $username);
            $username = str_ireplace('https://', '', $username);
        } else {
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
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function afterFind($data) {
        if(is_array($data)) {
            foreach($data as $key => $item) {
                $checkModels = array('WithIdentity', 'Identity');
                foreach($checkModels as $modelName) {
                    if(isset($item[$modelName]['username'])) {
                        $username = $this->splitUsername($item[$modelName]['username']);
                        $item[$modelName]['local_username']  = $username['local_username'];
                        $item[$modelName]['username']       = $username['username'];
                        $item[$modelName]['namespace']      = $username['namespace'];
                        $item[$modelName]['local']          = $username['local'];
                        $data[$key] = $item;
                    }
                }
            }
        }
        return $data;
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
        preg_match('/<geo:lat>(.*)<\/geo:lat>/i', $content, $latitude);
        preg_match('/<geo:long>(.*)<\/geo:long>/i', $content, $longitude);
        
        preg_match_all('/<foaf:OnlineAccount rdf:about="(.*)".*\/>/i', $content, $accounts);
        preg_match_all('/<foaf:accountServiceHomepage rdf:resource="(.*)".*\/>/iU', $content, $services);
        preg_match_all('/<foaf:accountName>(.*)<\/foaf:accountName>/i', $content, $usernames);
        
        
        #echo 'NOSERUB-ID<pre>'; print_r($noserub_id); echo '</pre>';
        #echo 'FIRSTNAME<pre>'; print_r($firstname); echo '</pre>';
        #echo 'LASTNAME<pre>'; print_r($lastname); echo '</pre>';
        #echo 'GENDER<pre>'; print_r($gender); echo '</pre>';
        #echo 'LATITUDE<pre>'; print_r($latitude); echo '</pre>';
        #echo 'LONGITUDE<pre>'; print_r($longitude); echo '</pre>';
        #echo 'ACCOUNTS<pre>'; print_r($accounts); echo '</pre>';
        #echo 'SERVICES<pre>'; print_r($services); echo '</pre>';
        #echo 'USERNAMES<pre>'; print_r($usernames); echo '</pre>';
        
        if(empty($noserub_id)) {
            return false;
        }
        
        $result = array('accounts' => array(),
                        'identity' => array());
        
        $result['identity']['firstname'] = $firstname ? $firstname[1] : '';
        $result['identity']['lastname']  = $lastname  ? $lastname[1]  : '';
        if($gender) {
            switch($gender[1]) {
                case 'female': $result['identity']['sex'] = 1; break;
                case 'male'  : $result['identity']['sex'] = 2; break;
                default      : $result['identity']['sex'] = 0;
            }
        } else {
            $result['identity']['sex'] = 0;
        }
        
        $result['identity']['latitude']  = $latitude  ? $latitude[1]  : 0;
        $result['identity']['longitude'] = $longitude ? $longitude[1] : 0;
        
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
        $data['identity']['last_sync'] = date('Y-m-d H:i:s');
        $this->save($data['identity']);
        
        return true;
    }
}
