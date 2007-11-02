<?php
 
class SyndicationsController extends AppController {
    var $uses = array('Syndication');
    var $helpers = array('form', 'html', 'nicetime', 'flashmessage');
    var $components = array('url', 'cdn');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function feed($url, $internal_call = false, $datetime_last_upload = '2007-01-01') {
        $feed_types = array('rss' => 'text/xml', 'js' => 'text/javascript');
        $extension = '';
        $hash = '';
        if(preg_match('/(.*)\.([0-9a-zA-Z]*)$/', $url, $match) == 1) {
            $extension = strtolower($match[2]);
            $hash = $match[1];
        }
        
        if($extension && isset($feed_types[$extension])) {
            # if we use the CDN for this, we will redirect directly to there,
            # but only, if this is not an internal call
            if(!$internal_call && defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
                $this->redirect('http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/feeds/'.$hash.'.'.$extension, '301', true);
            }
            
            # find syndication
            $this->Syndication->recursive = 1;
            $this->Syndication->expects('Syndication', 'Account', 'Identity');
            $data = $this->Syndication->findByHash($hash);

            # get all items for those accounts
            $items = array();
            if(is_array($data['Account'])) {
                foreach($data['Account'] as $account) {
                    if(defined('NOSERUB_USE_FEED_CACHE') && NOSERUB_USE_FEED_CACHE) {
                        $new_items = $this->Syndication->Account->Feed->access($account['id'], 5, false);
                    } else {
                        $new_items = $this->Syndication->Account->Service->feed2array($data['Identity']['local_username'], $account['service_id'], $account['service_type_id'], $account['feed_url'], 5, false);
                    }
                    if($new_items) {
                        $items = array_merge($items, $new_items);
                    }
                }
                usort($items, 'sort_items');
            }
            
            $this->set('syndication_name', $data['Syndication']['name']);
            $this->set('identity', $data['Identity']);
            $this->set('data', $items);

            # decide, wether to render the feed directly,
            # or uploading it to the CDN.
            if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
                # check, if the items are new enough, so we need
                # to do an upload
                $datetime_newest_item = isset($items[0]['datetime']) ? $items[0]['datetime'] : '2007-10-01';
                if($datetime_newest_item > $datetime_last_upload) {
                    # we need to upload
                    foreach($feed_types as $feed_type => $mime_type) {
                        ob_start();                
                        $this->layout = 'feed_' . $feed_type;
                        $this->render('feed');
                        $content = ob_get_contents();
                        $this->cdn->writeContent('feeds/'.$hash.'.'.$feed_type, $mime_type, $content);
                        ob_end_clean();
                    }                
                    return true;
                } 
                return false;
            } else {
                # just render it
                $this->layout = 'feed_'.$extension;
            }
        }
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Syndication->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        # get all the syndications for logged in user
        $this->Syndication->recursive = 0;
        $this->Syndication->expects('Syndication');
        $this->set('data', $this->Syndication->findAllByIdentityId($session_identity['id']));
        
        $this->set('session_identity', $session_identity);
        $this->set('headline', 'Configure Feeds from your activities and accounts');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function delete() {
        $username       = isset($this->params['username'])       ? $this->params['username']       : '';
        $syndication_id = isset($this->params['syndication_id']) ? $this->params['syndication_id'] :  0;
        $splitted = $this->Syndication->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username'] ||
           $syndication_id == 0) {
            # this is not the logged in user, or invalid syndication_id
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # check, if the syndication_id belongs to the logged in user
        $this->Syndication->recursive = 0;
        $this->Syndication->expects('Syndication');
        if(1 == $this->Syndication->findCount(array('id' => $syndication_id, 'identity_id' => $session_identity['id']))) {
            # everything ok, we can delete now...
            $this->Syndication->delete($syndication_id);
            $this->flashMessage('success', 'Feed deleted.');
            $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/feeds/');
        	$this->redirect($url, null, true);
        }
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Syndication->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            # this is not the logged in user
            $url = $this->url->http('/');
            $this->redirect($url, null, true);
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
                if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
                    $this->feed($data['Syndication']['hash'].'.rss', true);
                }
            } 
            
            $this->flashMessage('success', 'Feed added.');            
            $url = $this->url->http('/' . urlencode(strtolower($session_identity['local_username'])) . '/settings/feeds/');
        	$this->redirect($url, null, true);
        } else {
            # get all accounts from this user, that have feeds
            $this->Syndication->Account->recursive = 1;
            $this->Syndication->Account->expects('Account', 'Service');
            $accounts = $this->Syndication->Account->findAll(array('Account.identity_id' => $session_identity['id'],
                                                                   'Account.feed_url <> ""'));
            $this->set('accounts', $accounts);

            # get all accounts from this users contacts
            $this->Syndication->Identity->Contact->recursive = 1;
            $this->Syndication->Identity->Contact->expects('Contact.WithIdentity', 
                                                           'WithIdentity.Account.Service');
           
            $contacts = $this->Syndication->Identity->Contact->findAllByIdentityId($session_identity['id']);
            
            # now go through all contacts to get accounts and services
            foreach($contacts as $key => $value) {
                $this->Syndication->Account->recursive = 1;
                $this->Syndication->Account->expects('Account', 'Service');
                $contacts[$key]['WithIdentity']['Account'] = $this->Syndication->Account->findAll(array('identity_id' => $value['WithIdentity']['id'], 'feed_url != ""')); 
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
        
        $this->set('headline', 'Add new Feed');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function shell_upload() {
        $uploaded = array();

        if(!defined('NOSERUB_USE_CDN') || !NOSERUB_USE_CDN) {
            # we don't need to do any upload
            $this->set('data', $uploaded);
            $this->render();
            exit;
        }
        
        # I do this like this and not through a LIMIT of 250, because
        # then more than one task could run at once, without doing any harm
        for($i=0; $i<250; $i++) {
            # no two refresh's within 14 minutes
            $last_upload = date('Y-m-d H:i:s', strtotime('-15 minutes'));
            
            $this->Syndication->recursive = 0;
            $this->Syndication->expects('Syndication');
            $data = $this->Syndication->findAll(array('Syndication.last_upload < "' . $last_upload . '"'), null, 'Syndication.last_upload ASC, Syndication.modified DESC', 1);
            foreach($data as $item) {
                # save the old last_update timestamp
                $datetime_last_upload = $item['Syndication']['last_upload'];
                
                # set the last_upload right now, so a parallel running task
                # would not get it, while we are uploading the data
                $this->Syndication->id = $item['Syndication']['id'];
                $this->Syndication->saveField('last_upload', date('Y-m-d H:i:s'));
                
                # call the internal method
                # it's not important which feed_type we want, as all
                # available will be created and being uploaded
                if($this->feed($item['Syndication']['hash'].'.rss', true, $datetime_last_upload)) {
                    $uploaded[] = $item['Syndication']['hash'];
                } else {
                    # in this case, we need to set the old timestamp again. because it could
                    # happen, that a rss feed is updated some time after now, but with an item
                    # older than now.
                    $this->Syndication->id = $item['Syndication']['id'];
                    $this->Syndication->saveField('last_upload', $datetime_last_upload);
                }
            }
        }
        $this->layout = 'shell';
        $this->set('uploaded', $uploaded);
        $this->render();
    }
}
