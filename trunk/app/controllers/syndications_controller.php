<?php
 
class SyndicationsController extends AppController {
    var $uses = array('Syndication');
    var $helpers = array('form', 'html', 'nicetime');
    var $components = array('url', 'cdn');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function feed($url) {
        $feed_types = array('rss' => 'text/xml', 'js' => 'text/javascript');
        $extension = '';
        $hash = '';
        if(preg_match('/(.*)\.([0-9a-zA-Z]*)$/', $url, $match) == 1) {
            $extension = strtolower($match[2]);
            $hash = $match[1];
        }
        
        if($extension && isset($feed_types[$extension])) {
            # if we use the CDN for this, we will redirect directly to there
            if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
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
                        $new_items = $this->Syndication->Account->Service->feed2array($username, $account['service_id'], $account['service_type_id'], $account['feed_url'], 5, false);
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
                foreach($feed_types as $feed_type => $mime_type) {
                    ob_start();                
                    $this->layout = 'feed_'.$feed_type;
                    $this->render('feed');
                    $content = ob_get_contents();
                    $this->cdn->writeContent('feeds/'.$hash.'.'.$feed_type, $mime_type, $content);
                    ob_end_clean();
                }
                exit;
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
        $this->set('headline', 'Configure Feeds from your activites and accounts');
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
        
        # check, if the syndication_id belongs to the logged in user
        $this->Syndication->recursive = 0;
        $this->Syndication->expects('Syndication');
        if(1 == $this->Syndication->findCount(array('id' => $syndication_id, 'identity_id' => $session_identity['id']))) {
            # everything ok, we can delete now...
            $this->Syndication->delete($syndication_id);
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
            } 
                        
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
            $this->Syndication->Identity->Contact->recursive = 3;
            $this->Syndication->Identity->Contact->expects('Contact.WithIdentity', 
                                                           'WithIdentity.Account.Service');
            $contacts = $this->Syndication->Identity->Contact->findAllByIdentityId($session_identity['id']);
            $this->set('contacts', $contacts);
            
            # gather all accounts that the user may add to a syndication 
            # and save it in the session
            $valid_accounts = array();
            foreach($accounts as $item) {
                $valid_accounts[] = $item['Account']['id'];
            }
            foreach($contacts as $contact) {
                foreach($contact['WithIdentity']['Account'] as $item) {
                    $valid_accounts[] = $item['id'];
                }
            }
            $this->Session->write('Syndication.add.valid_accounts', $valid_accounts);
        }
        
        $this->set('headline', 'Add new Feed');
    }
}