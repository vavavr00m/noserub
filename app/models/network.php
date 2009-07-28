<?php
/* SVN FILE: $Id:$ */
 
class Network extends AppModel {
    public $hasMany = array('Identity', 'Group', 'Admin');
    public $hasAndBelongsToMany = array(
            'NetworkSubscriber' => array(
                'className'  => 'Identity',
                'joinTable'  => 'network_subscriptions',
                'foreignKey' => 'network_id',
                'associationForeignKey' => 'identity_id'
            )
    );
    
    public $validate = array(
        'url' => array(
            'rule'    => array('validateUrl'),
            'message' => '' # must be set via beforeValidate due to using gettext
        )
    );
    
    public function __construct() {
        parent::__construct();
        $this->validate['url']['message'] = __('The URL must start with http:// or https://', true);
    }
    
	public function getEnabled() {
		return $this->find('all', array(
            'contain' => false,
            'conditions' => array(
                'disabled' => 0,
                'is_local' => 1
            ),
            'order' => 'last_sync ASC'
        ));        
	}
	
	public function getNumberOfUsers($network_id) {
		$this->Identity->contain();
        $conditions = array(
            'network_id' => $network_id,
            'email <>'   => '',
            'hash'       => '',
            'NOT username LIKE "%@"'
        );
        
        return $this->Identity->find('count', array('conditions' => $conditions));
	}
	
	public function getSubscribable() {
		return $this->find('all',array(
            'contain' => array(
                'NetworkSubscriber' => array(
                    'conditions' => array(
                        'identity_id' => Context::read('logged_in_identity')
                    )
                )
            ),
            'conditions' => array(
                'disabled' => 0,
                'allow_subscriptions' => 1,
                'is_local' => 0
            ),
            'order' => 'name ASC'
        ));        
	}
	
	public function validateUrl($data) {
	    $url = strtolower($data['url']);
	    
	    return strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
	}
	
	public function save($data = null, $validate = true, $fieldList = array()) {
		if(isset($data['Network']['url'])) {
			$data['Network']['url'] = $this->ensureUrlEndsWithSlash($data['Network']['url']);
		}
		
		return parent::save($data, $validate, $fieldList);
	}
	
	/**
	 * subscribes an identity to the current network ($this->id)
	 */
	public function subscribe($identity_id) {
	    if(!$this->field('allow_subscriptions')) {
	        return;
	    }
	    
	    $subscribed = $this->find('first', array(
	        'contain' => array(
	            'NetworkSubscriber' => array(
	                'conditions' => array(
	                    'NetworkSubscription.identity_id' => $identity_id,
	                    'NetworkSubscription.network_id'  => $this->id
	                )
	            ),
	        ),
	        'fields' => array('Network.id')
	    ));
	    
	    if(empty($subscribed['NetworkSubscriber'])) {
	        $data = array(
	            'Network' => array('id' => $this->id),
	            'NetworkSubscriber' => array(
	                'identity_id' => $identity_id
	            )
	        );
	        
	        $this->create();
	        $this->save($data);
	    }
	}
	
	public function unsubscribe($identity_id) {
	    $subscribed = $this->find('first', array(
	        'contain' => array(
	            'NetworkSubscriber' => array(
	                'conditions' => array(
	                    'NetworkSubscription.identity_id' => $identity_id,
	                    'NetworkSubscription.network_id'  => $this->id
	                )
	            ),
	        ),
	        'fields' => array('Network.id')
	    ));
	    
	    if(!empty($subscribed['NetworkSubscriber'])) {
	        $this->NetworkSubscription->deleteAll(
	            array(
                    'identity_id' => $identity_id,
                    'network_id'  => $this->id
	            )
	        );
	    }
	}
	
    /**
     * retrieves a list of networks from http://noserub.com/networks
     * and syncs this list with the ones in the local database
     */
    public function sync() {
        App::import('Vendor', 'WebExtractor');
        $json_data = WebExtractor::fetchUrl('http://noserub.com/networks');
        
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        $data = Zend_Json::decode($json_data);
        
        if(!$data) {
            return __('ERROR: could not connect to http://noserub.com/networks', true);
        }
        
        $updated_networks = array();
        foreach($data as $item) {
            if($item['url'] == Context::read('network.url')) {
                # we don't need to set ourselves to that list
                continue;
            }
            
            # check, if we already have this network
            $this->contain();
            $network = $this->find(
                'first',
                array(
                    'conditions' => array(
                        'url' => $item['url']
                    )
                )
            );
            
            if(!$network) {
                # create it!
                $network = array(
                    'name'      => $item['url'],
                    'url'       => $item['url'],
                    'disabled'  => $item['deleted'],
                    'last_sync' => '2008-01-01 00:00:00'
                );
                $this->create();
                $this->save($network);
                $updated_networks[] = $item['url'];
            } else {
                # see, if it was disabled. a network can not be enabled by sync,
                # only disabled, as admin of this installation should be
                # able to disable a network without it being enabled again
                if($item['deleted'] && !$network['Network']['disabled']) {
                    $this->id = $network['Network']['id'];
                    $this->saveField('disabled', 1);
                    $updated_networks[] = $item['url'];
                }
            }
        }
        
        if(!$updated_networks) {
            return __('no networks updated', true);
        }
        
        return $updated_networks;
    }
    
    private function ensureUrlEndsWithSlash($url) {
    	return (strpos(strrev($url), '/') === 0) ? $url : $url . '/';
    }
}