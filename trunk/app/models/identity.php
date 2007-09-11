<?php
/* SVN FILE: $Id:$ */
 
class Identity extends AppModel {
    var $hasMany = array('Contact', 'Account');
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
        if($this->findCount(array('Identity.username = "' . $value . '"')) > 0) {
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
        $saveable = array('is_local', 'username', 'password', 'email', 'hash', 'created', 'modified');
        if(!$this->save($data, true, $saveable)) {
            return false;
        }
        
        # send out verification mail
        $msg  = 'Welcome to NoseRub!' . "\n\n";
        $msg .= 'please click here to verify you email address:' ."\n";
        $msg .= FULL_BASE_URL . '/pages/verify/' . $data['Identity']['hash'] . '/' . "\n\n";
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
        # first, remove http:// and https://
        $username = str_replace('http://', '', $username);
        $username = str_replace('https://', '', $username);
        
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
            $username = FULL_BASE_URL . '/' . $local_username;
            $username = str_replace('http://', '', $username);
            $username = str_replace('https://', '', $username);
        } else {
            $local_username = array_pop($splitted);
            $username = join('/', $splitted) . '/' . $local_username;
        }

        # test, wether we have a namespace here, or not
        $local_username_namespace = split('@', $local_username);
        
        # test, if this is a local contact, or not
        $server_name = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
        $server_name = str_replace('https://', '', $server_name);
        $local = strpos($username, $server_name) === 0;
        
        $result = array('username'       => $username,
                        'local_username' => $local_username,
                        'namespace'      => isset($local_username_namespace[1]) ? $local_username_namespace[1] : '',
                        'local'          => $local);
        
        return $result;
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
        preg_match_all('/<foaf:OnlineAccount rdf:about="(.*)".*\/>/i', $content, $accounts);
        preg_match_all('/<foaf:accountServiceHomepage rdf:resource="(.*)".*\/>/iU', $content, $services);
        preg_match_all('/<foaf:accountName>(.*)<\/foaf:accountName>/i', $content, $usernames);
        
        /*
        echo 'ACCOUNTS<pre>'; print_r($accounts); echo '</pre>';
        echo 'SERVICES<pre>'; print_r($services); echo '</pre>';
        echo 'USERNAMES<pre>'; print_r($usernames); echo '</pre>';
        */
        
        $result = array();
        
        if(is_array($accounts)) {
            # gather all account data
            foreach($accounts[1] as $idx => $account_url) {
                $account = array();
                
                if(strpos($services[1][$idx], 'NoseRubServiceType:') === 0) {
                    # this is service_id 8 => any RSS-Feed
                    $account['feed_url']    =  $account['service_url'];
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
                
                $result[] = $account; 
            }
        } else {
            return false;
        }
        $this->log(print_r($result, 1), LOG_DEBUG);
        return $result;
    }
}