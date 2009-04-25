<?php

class SyndicationsController extends ApiAppController {
	public $uses = array('Syndication');
	public $components = array('OauthServiceProvider');
	private $identity_id = null;
	
	public function beforeFilter() {
		if (isset($this->params['username'])) {
    		$identity = $this->Api->getIdentity();
        	$this->Api->exitWith404ErrorIfInvalid($identity);
        	$this->identity_id = $identity['Identity']['id'];
		} else {
    		$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
			$accessToken = ClassRegistry::init('AccessToken');
			$this->identity_id = $accessToken->field('identity_id', array('token_key' => $key));
		}
	}
	
	public function get_feeds() {
        $this->Syndication->contain();
        $data = $this->Syndication->findAllByIdentityId($this->identity_id, array('name', 'hash'));
        
        if(Configure::read('NoseRub.use_cdn')) {
            $feed_url = 'http://s3.amazonaws.com/' . Configure::read('NoseRub.cdn_s3_bucket') . '/feeds/';
        } else {
        	$identity = $this->getIdentity($this->identity_id);
        	$feed_url = Router::url('/' . $identity['Identity']['local_username'] . '/feeds/', true);
        }
        
        # replace the hash by the actual feed url
        foreach($data as $idx => $item) {
            $data[$idx]['Syndication']['url'] = array(
                'rss'  => $feed_url . $item['Syndication']['hash'] . '.rss',
                'json' => $feed_url . $item['Syndication']['hash'] . '.js',
                'sphp' => $feed_url . $item['Syndication']['hash'] . '.sphp'
            );
            unset($data[$idx]['Syndication']['hash']);
        }

        # look for the generic feeds
        if($identity['Identity']['generic_feed']) {
            if(Configure::read('NoseRub.use_cdn')) {
                $feed_url .= $identity['Identity']['local_username'] . '.';
            }
            $data[] = array(
                'Syndication' => array(
                    'name' => 'Generic Feed',
                    'url'  => array(
                        'rss'  => $feed_url . 'rss',
                        'json' => $feed_url . 'js',
                        'sphp' => $feed_url . 'sphp'
                    )
                )
            );
        }
        
        $this->set('data', $data);
        
        $this->Api->render();
    }
    
	private function getIdentity($identity_id) {
    	$this->Syndication->Identity->contain();
        $identity = $this->Syndication->Identity->findById($identity_id);
        $identity['Identity'] = array_merge($identity['Identity'], $this->Syndication->Identity->splitUsername($identity['Identity']['username']));
        
        return $identity;
    }
}