<?php

class TwitterAccount extends AppModel {
	public $belongsTo = array('Identity');
	
	public function deleteByIdentityId($identity_id) {
		return $this->deleteAll(array('identity_id' => $identity_id));
	}

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
	
	public function updateStatus($identity_id, $text) {
		if (Context::isTwitterFeatureEnabled() === false) {
            return false;       
        }

        $this->contain();
        $data = $this->findByIdentityId($identity_id);
        
        if (empty($data)) {
        	return false;
        }
        
        $key = $data['TwitterAccount']['access_token_key'];
        $secret = $data['TwitterAccount']['access_token_secret'];
        $url = 'http://www.twitter.com/statuses/update.json';
        
        App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'oauth_consumer.php'));
        $consumer = new OAuth_Consumer(Context::read('network.twitter_consumer_key'), Context::read('network.twitter_consumer_secret'));
        $result = $consumer->post($key, $secret, $url, array('status' => $text));
        
        $success = true;
        
		if(!$result) {
            $this->log('No data returned by twitter when trying to send update for: ' . $key);
            $success = false;
        }
        
		App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        $status = Zend_Json::decode($result);
        if(!isset($status['id'])) {
            $this->log('Unexpected data returned by twitter API when trying to send update for: ' . $key);
            $this->log(print_r($result, 1), LOG_DEBUG);
            $this->log(print_r($status, 1), LOG_DEBUG);
            $success = false;
        }
        
        return $success;
	}
}
