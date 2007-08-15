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
        $username = $data['Identity']['username'];
        if(strpos($username, '@' . NOSERUB_DOMAIN) === false) {
            # user can log in with <USERNAME> and <USERNAME>@<NOSERUB_DOMAIN>
            $username = $username . '@' . NOSERUB_DOMAIN;
        }
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
        $this->create();
        $data['Identity']['username'] = $data['Identity']['username'] . '@' . NOSERUB_DOMAIN;
        $data['Identity']['password'] = md5($data['Identity']['passwd']);
        $data['Identity']['hash'] = md5(time().$data['Identity']['username']);
        $saveable = array('username', 'password', 'email', 'hash', 'created', 'modified');
        if(!$this->save($data, true, $saveable)) {
            return false;
        }
        
        # send out verification mail
        $msg  = 'Welcome to NoseRub!' . "\n\n";
        $msg .= 'please click here to verify you email address:' ."\n";
        $msg .= FULL_BASE_URL . '/pages/verify/' . $data['Identity']['username'] . '/' . $data['Identity']['hash'] . '/' . "\n\n";
        $msg .= 'If you do not click on this link, the account will automatically be deleted after 14 days.' . "\n\n";
        $msg .= 'Thanks!';
        
        if(!mail($data['Identity']['email'], 'Your NoseRub registration', $msg, 'From: info@noserub.com')) {
            LogError('verify mail could not be sent: '.$data['Identity']['email']);
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
    function verify($username, $hash) {
        # check, if there is a username with that hash
        $this->recursive = 0;
        $this->expects = array('Identity');
        $identity = $this->find(array('Identity.username' => $username, 'Identity.hash' => $hash));
        if($username && $hash && $identity) {
            # update the identity
            $this->id = $identity['Identity']['id'];
            return $this->saveField('hash', '');
        } else {
            return false;
        }
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function splitUsername($username) {
        $username_domain = split('@', $username);
        $username_namespace = split(':', $username_domain[0]);        
        
        $result = array('full_username' => $username,
                        'username'      => $username_namespace[0],
                        'namespace'     => isset($username_namespace[1]) ? $username_namespace[1] : '',
                        'domain'        => isset($username_domain[1])    ? $username_domain[1]    : '');
    
        $url = 'http://' . $result['domain'] . '/noserub/' . $result['username'];
        if($result['namespace']) {
            $url .= '@' . $result['namespace'];
        }

        $result['url'] = $url;

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
                        $item[$modelName]['full_username'] = $username['full_username'];
                        $item[$modelName]['username']      = $username['username'];
                        $item[$modelName]['namespace']     = $username['namespace'];
                        $item[$modelName]['domain']        = $username['domain'];
                        $item[$modelName]['url']           = $username['url'];
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
        $content = file_get_contents($url);
        if(!$content) {
            return false;
        }
        preg_match_all('/<foaf:OnlineAccount rdf:about="(.*)".*\/>/i', $content, $accounts);
        preg_match_all('/<foaf:accountServiceHomepage rdf:resource="(.*)".*\/>/iU', $content, $services);
        preg_match_all('/<foaf:accountName>(.*)<\/foaf:accountName>/i', $content, $usernames);
        preg_match_all('/<foaf:weblog rdf:resource="(.*)".*rdf:feed="(.*)".*\/>/i', $content, $blogs);
        
        /*
        echo 'ACCOUNTS<pre>'; print_r($accounts); echo '</pre>';
        echo 'SERVICES<pre>'; print_r($services); echo '</pre>';
        echo 'USERNAMES<pre>'; print_r($usernames); echo '</pre>';
        echo 'BLOGS<pre>'; print_r($blogs); echo '</pre>';
        */
        
        $result = array();
        
        if(is_array($accounts)) {
            # gather all account data
            foreach($accounts[1] as $idx => $account_url) {
                $account = array();
                $account['account_url'] = $account_url;
                $account['service_url'] = isset($services[1][$idx])  ? $services[1][$idx]  : '';
                $account['username']    = isset($usernames[1][$idx]) ? $usernames[1][$idx] : '';
                $info = $this->Account->Service->getInfo($account['service_url'], $account['username']);
                $account['service_id']      = isset($info['service_id'])      ? $info['service_id']      : 0;
                $account['service_type_id'] = isset($info['service_type_id']) ? $info['service_type_id'] : 0;
                $account['feed_url']        = isset($info['feed_url'])        ? $info['feed_url']        : '';
                
                $result[] = $account; 
            }
            # gather all blog data
            foreach($blogs[1] as $idx => $blog_url) {
                $blog = array();
                $blog['account_url']     = $blog_url;
                $blog['feed_ur']         = isset($blogs[2][$idx]) ? $blogs[2][$idx] : '';
                $blog['service_id']      = 7;
                $blog['service_type_id'] = 3;
                $result[] = $blog;
            }
        } else {
            return false;
        }
        
        return $result;
    }
}