<?php
 
class SyndicationsController extends AppController {
    public $uses = array('Syndication');
    public $helpers = array('form', 'html', 'nicetime', 'flashmessage');
    public $components = array('url', 'cdn', 'api', 'OauthServiceProvider');
    
    public function feed($url, $internal_call = false, $datetime_last_upload = '2007-01-01') {
        $this->checkUnsecure();
        $feed_types = array(
            'rss'  => 'text/xml', 
            'js'   => 'text/javascript',
            'sphp' => 'text/text');
        $extension = '';
        $hash = '';
        if(preg_match('/(.*)\.([0-9a-zA-Z]*)$/', $url, $match) == 1) {
            $extension = strtolower($match[2]);
            $hash = $match[1];
        }
        
        if(!$extension) {
            if(isset($feed_types[$url])) {
                $extension = $url;
                $hash = 'generic';
            }
        }
        if($extension && isset($feed_types[$extension])) {
            # if we use the CDN for this, we will redirect directly to there,
            # but only, if this is not an internal call
            if(!$internal_call && NOSERUB_USE_CDN) {
                $this->redirect('http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/feeds/'.$hash.'.'.$extension, '301');
            }

            if($hash === 'generic') {
                $username = isset($this->params['username']) ? $this->params['username'] : '';
                $splitted = $this->Syndication->Identity->splitUsername($username);
                $username = $splitted['username'];
                $this->Syndication->Identity->contain();
                $identity = $this->Syndication->Identity->findByUsername($username);
                $items = array();
                
                if($identity && $identity['Identity']['generic_feed']) {
                    $conditions = array(
                        'identity_id' => $identity['Identity']['id']
                    );
                    $items = $this->Syndication->Identity->Entry->getForDisplay($conditions, 25);
                    usort($items, 'sort_items');
                }
                $this->set('syndication_name', 'Generic Feed');
                $this->set('identity', $identity['Identity']);
            } else {
                # find syndication
                $this->Syndication->contain(array('Account', 'Identity'));
                $data = $this->Syndication->findByHash($hash);
                
                # get all items for those accounts
                $items = array();
                $conditions = array(
                    'account_id' => Set::extract($data, 'Account.{n}.id')
                );
                $items = $this->Syndication->Identity->Entry->getForDisplay($conditions, 25);
                usort($items, 'sort_items');
                $this->set('syndication_name', $data['Syndication']['name']);
                $this->set('identity', $data['Identity']);
            }
            
            $this->set('data', $items);
            # decide, wether to render the feed directly,
            # or uploading it to the CDN.
            if(NOSERUB_USE_CDN) {
                # check, if the items are new enough, so we need
                # to do an upload
                $datetime_newest_item = isset($items[0]['datetime']) ? $items[0]['datetime'] : '2007-10-01';
                if($datetime_newest_item > $datetime_last_upload) {
                    # we need to upload
                    foreach($feed_types as $feed_type => $mime_type) {
                        $this->layout = 'feed_' . $feed_type;
                        $content = $this->render('feed');
                        if($hash === 'generic') {
                            $hash = $identity['Identity']['local_username'];
                        }
                        if($content) {
                            $this->cdn->writeContent('feeds/'.$hash.'.'.$feed_type, $mime_type, $content);
                        }
                        $this->output = ''; 
                    }                
                } 
                return true;
            } else {
                # just render it
                $this->layout = 'feed_'.$extension;
            }
        }
    }
    
    public function index() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Syndication->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        if($this->data) {
            $this->ensureSecurityToken();

            $this->Syndication->Identity->id = $session_identity['id'];
            if($this->Syndication->Identity->saveField('generic_feed', $this->data['Identity']['generic_feed'])) {
                $this->flashMessage('success', __('Settings saved', true));
            } else {
                $this->flashMessage('error', __('Something went wrong', true));
            }
        } else {
            # need to fetch it here, because some people could still be logged in
            # when this updates happens
            $this->Syndication->Identity->id = $session_identity['id'];
            $generic_feed = $this->Syndication->Identity->field('generic_feed');
            $this->data = array(
                'Identity' => 
                    array(
                        'generic_feed' => $generic_feed
                    )
            );
        }
        
        # get all the syndications for logged in user
        $this->Syndication->contain();
        $this->set('data', $this->Syndication->findAllByIdentityId($session_identity['id']));
        
        $this->set('session_identity', $session_identity);
        $this->set('headline', __('Configure Feeds from your activities and accounts', true));
    }
    
    public function delete() {
        $username       = isset($this->params['username'])       ? $this->params['username']       : '';
        $syndication_id = isset($this->params['syndication_id']) ? $this->params['syndication_id'] :  0;
        $splitted = $this->Syndication->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] ||
           $syndication_id == 0) {
            # this is not the logged in user, or invalid syndication_id
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # check, if the syndication_id belongs to the logged in user
        if($this->Syndication->hasAny(array('id' => $syndication_id, 'identity_id' => $session_identity['id']))) {
            # everything ok, we can delete now...
            $this->Syndication->delete($syndication_id);
            $this->flashMessage('success', __('Feed deleted.', true));
            $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/feeds/');
        	$this->redirect($url);
        }
    }
    
    public function add() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Syndication->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url);
        }
        
        if($this->data) {
            $valid_accounts = $this->Session->read('Syndication.add.valid_accounts');
            if($this->data['Syndication']['name'] != '') {
                # get all accounts, that should be added to the syndication
                $accounts = isset($this->data['Syndication']['Account']) ? $this->data['Syndication']['Account'] : array();
                if(isset($this->data['Syndication']['Contact'])) {
                    foreach($this->data['Syndication']['Contact'] as $contact) {
                        foreach($contact['Account'] as $item) {
                            $accounts[] = $item;
                        }
                    }
                }

                # make sure, no "forbidden" accounts were entered through the form
                $new_accounts = array();
                for($i=0; $i<count($accounts); $i++) {
                    if(in_array($accounts[$i], $valid_accounts, true)) {
                        $new_accounts[] = $accounts[$i];
                    }
                }

                # create the new syndication
                $data = array('Syndication' => array('name'        => $this->data['Syndication']['name'],
                                                     'identity_id' => $session_identity['id'],
                                                     'hash'        => md5(time().$this->data['Syndication']['name'])),
                              'Account' => array('Account' => $new_accounts));
                $this->Syndication->create();
                $this->Syndication->save($data);
                
                # no also create it initially, if we use a CDN
                if(NOSERUB_USE_CDN) {
                    $this->feed($data['Syndication']['hash'].'.rss', true);
                }
            } 
            
            $this->flashMessage('success', __('Feed added.', true));
            $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/feeds/');
        	$this->redirect($url);
        } else {
            # get all accounts from this user, that have feeds
            $this->Syndication->Account->contain('Service');
            $accounts = $this->Syndication->Account->find('all', array('conditions' => array('Account.identity_id' => $session_identity['id'],
            																				 'Account.feed_url <> ""')));
            $this->set('accounts', $accounts);

            # get all accounts from this users contacts
            $this->Syndication->Identity->Contact->contain(array('WithIdentity', 'WithIdentity.Account.Service'));
           
            $contacts = $this->Syndication->Identity->Contact->findAllByIdentityId($session_identity['id']);
            
            # now go through all contacts to get accounts and services
            foreach($contacts as $key => $value) {
                $this->Syndication->Account->contain('Service');
                $contacts[$key]['WithIdentity']['Account'] = $this->Syndication->Account->find('all', array('conditions' => array('identity_id' => $value['WithIdentity']['id'],
                																												  'feed_url != ""')));  
            }
            $this->set('contacts', $contacts);
            
            # gather all accounts that the user may add to a syndication 
            # and save it in the session
            $valid_accounts = array();
            foreach($accounts as $item) {
                $valid_accounts[] = $item['Account']['id'];
            }
            foreach($contacts as $contact) {
                foreach($contact['WithIdentity']['Account'] as $item) {
                    $valid_accounts[] = $item['Account']['id'];
                }
            }
            $this->Session->write('Syndication.add.valid_accounts', $valid_accounts);
        }
        
        $this->set('headline', __('Add new Feed', true));
        $this->set('base_url_for_avatars', $this->Syndication->Identity->getBaseUrlForAvatars());
    }
    
    /**
     * API method to get a list of syndications, that the user created
     */
    public function api_get() {
    	if (isset($this->params['username'])) {
    		$identity = $this->api->getIdentity();
        	$this->api->exitWith404ErrorIfInvalid($identity);
        	$identity_id = $identity['Identity']['id'];
		} else {
    		$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
			$accessToken = ClassRegistry::init('AccessToken');
			$identity_id = $accessToken->field('identity_id', array('token_key' => $key));
		}
        
        $this->Syndication->contain();
        $data = $this->Syndication->findAllByIdentityId($identity_id, array('name', 'hash'));
        
        if(NOSERUB_USE_CDN) {
            $feed_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/feeds/';
        } else {
        	if (!isset($identity)) {
        		$identity = $this->getIdentity($identity_id);
        	}
        	$url = Router::url('/' . $identity['Identity']['local_username']);
            $feed_url = NOSERUB_FULL_BASE_URL . $url . '/feeds/';
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
            if(NOSERUB_USE_CDN) {
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
        
        $this->api->render();
    }
    
    public function shell_upload() {
        $uploaded = array();

        if(!NOSERUB_USE_CDN) {
            # we don't need to do any upload
            $this->layout = 'shell';
            $uploaded[] = __('none - no CDN defined in noserub.php', true);
            $this->set('uploaded', $uploaded);
            $this->render();
            return;
        }
        
        # I do this like this and not through a LIMIT of 250, because
        # then more than one task could run at once, without doing any harm
        for($i=0; $i<250; $i++) {
            # no two refresh's within 14 minutes
            $last_upload = date('Y-m-d H:i:s', strtotime('-15 minutes'));
            
            $this->Syndication->contain();
            $data = $this->Syndication->find(
                'first', 
                array(
                    'conditions' => array(
                        'Syndication.last_upload <' => $last_upload
                    ),
            		'order' => array(
            		    'Syndication.last_upload ASC', 
            		    'Syndication.modified DESC'
            		)
            	)
            );
            if($data) {
                # save the old last_update timestamp
                $datetime_last_upload = $data['Syndication']['last_upload'];
                
                # set the last_upload right now, so a parallel running task
                # would not get it, while we are uploading the data
                $this->Syndication->id = $data['Syndication']['id'];
                $this->Syndication->saveField('last_upload', date('Y-m-d H:i:s'));
                
                # call the internal method
                # it's not important which feed_type we want, as all
                # available will be created and being uploaded
                if($this->feed($data['Syndication']['hash'].'.rss', true, $datetime_last_upload)) {
                    $uploaded[] = $data['Syndication']['hash'];
                } else {
                    # in this case, we need to set the old timestamp again. because it could
                    # happen, that a rss feed is updated some time after now, but with an item
                    # older than now.
                    $this->Syndication->id = $data['Syndication']['id'];
                    $this->Syndication->saveField('last_upload', $datetime_last_upload);
                }
            }
            
            # also upload a generic feed
            $this->Syndication->Identity->contain();
            $data = $this->Syndication->Identity->find(
                'first',
                array(
                    'conditions' => array(
                        'Identity.generic_feed' => 1,
                        'Identity.last_generic_feed_upload <' => $last_upload
                    ),
                    'order' => 'Identity.last_generic_feed_upload ASC'
                )
            );
            if($data) {
                # save the old last_update timestamp
                $datetime_last_upload = $data['Identity']['last_generic_feed_upload'];
            
                # set the last_upload right now, so a parallel running task
                # would not get it, while we are uploading the data
                $this->Syndication->Identity->id = $data['Identity']['id'];
                $this->Syndication->Identity->saveField('last_generic_feed_upload', date('Y-m-d H:i:s'));
            
                # call the internal method
                # it's not important which feed_type we want, as all
                # available will be created and being uploaded
                $this->params['username'] = $data['Identity']['username'];
                if($this->feed('rss', true, $datetime_last_upload)) {
                    $uploaded[] = $data['Identity']['username'];
                } else {
                    # in this case, we need to set the old timestamp again. because it could
                    # happen, that a rss feed is updated some time after now, but with an item
                    # older than now.
                    $this->Syndication->Identity->id = $data['Identity']['id'];
                    $this->Syndication->Identity->saveField('last_generic_feed_upload', $datetime_last_upload);
                }
            }    
        }
        $this->layout = 'shell';
        $this->set('uploaded', $uploaded);
        $this->render();
    }
    
    private function getIdentity($identity_id) {
    	$this->Syndication->Identity->contain();
        $identity = $this->Syndication->Identity->findById($identity_id);
        $identity['Identity'] = array_merge($identity['Identity'], $this->Syndication->Identity->splitUsername($identity['Identity']['username']));
        
        return $identity;
    }
}