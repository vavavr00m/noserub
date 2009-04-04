<?php

class TwitterAccount extends AppModel {
	public $belongsTo = array('Identity');
	
	public function saveAccessToken($identity_id, $access_token_key, $access_token_secret) {
		$id = $this->field('id', array('identity_id' => $identity_id));

		$data[$this->name]['id'] = $id ? $id : '';
		$data[$this->name]['identity_id'] = $identity_id;
		$data[$this->name]['access_token_key'] = $access_token_key;
		$data[$this->name]['access_token_secret'] = $access_token_secret;

		return $this->save($data);
	}

	public function update($identity_id, $data) {
		$id = $this->field('id', array('identity_id' => $identity_id));
		
		if ($id && $data[$this->name]['username'] == '') {
			$this->delete($id);
		} else {
			$data[$this->name]['id'] = $id ? $id : '';
			$data[$this->name]['identity_id'] = $identity_id;
			$this->save($data);
		}
	}
	
	/**
	 * many thanks to laconi.ca for this code!
     * can be found in lib/util.php there.
	 */
	public function updateStatus($identity_id, $text) {
		if (Configure::read('NoseRub.allow_twitter_bridge') === false) {
            return false;       
        }

        $this->contain();
        $data = $this->findByIdentityId($identity_id);
        if($data['TwitterAccount']['bridge_active'] != 1) {
            return false;
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
    		CURLOPT_USERAGENT		=> Configure::read('noserub.user_agent'),
    		CURLOPT_CONNECTTIMEOUT	=> 120,  
    		CURLOPT_TIMEOUT			=> 120,
    		
    		# Twitter is strict about accepting invalid "Expect" headers
            CURLOPT_HTTPHEADER      => array('Expect:')
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
        $data = curl_exec($ch);
        $error_msg = curl_error($ch);
        
        $success = true;
        
        if($error_msg) {
            $this->log('cURL error (' . $error_msg . ') when trying to send msg to twitter for ' . $twitter_username, LOG_DEBUG);
            $success = false;
        }
        
        if(!$data) {
            $this->log('No data returned by twitter when trying to send update for:' . $twitter_username);
            $success = false;
        }
        
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        $status = Zend_Json::decode($data);
        if(!isset($status['id'])) {
            $this->log('Unexpected data returned by twitter API when trying to send update for: ' . $twitter_username);
            $this->log(print_r($data, 1), LOG_DEBUG);
            $this->log(print_r($status, 1), LOG_DEBUG);
            $success = false;
        }
        
        curl_close($ch);
        
        return $success;
	}
}
