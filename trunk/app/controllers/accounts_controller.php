<?php
/* SVN FILE: $Id:$ */
 
class AccountsController extends AppController {
    var $uses = array('Account');
    var $helpers = array('form');
    
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        
        # get identity that is displayed
        $this->Account->Identity->recursive = 0;
        $this->Account->Identity->expects('Identity');
        $identity = $this->Account->Identity->findByUsername($username);
        if(!$identity) {
            # this identity is not here
            $this->redirect('/');
            exit;
        }
        $this->set('about_identity', $identity['Identity']);
        
        # get all accounts
        $this->Account->recursive = 1;
        $this->Account->expects('Account.Account', 'Account.Service', 'Service.Service');
        $this->set('data', $this->Account->findAllByIdentity_id($identity['Identity']['id']));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_step_1() {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        # reset session
        $this->Session->delete('Service.add.account.to.identity_id');
        $this->Session->delete('Service.add.id');
        
        # only logged in users can add accounts
        if(!$identity_id) {
            # this user is not logged in
            $this->redirect('/');
            exit;
        }
        
        # check, if logged in user may add accounts
        
        # get identity for which accounts should be added
        $this->Account->Identity->recursive = 0;
        $this->Account->Identity->expects('Identity');
        $identity = $this->Account->Identity->findByUsername($username);
        
        if($identity['Identity']['id'] != $identity_id) {
            # identity is not the logged in user
            
            # get logged in identity
            $this->Account->Identity->recursive = 0;
            $this->Account->Identity->expects('Identity');
            $logged_in_identity = $this->Account->Identity->findById($identity_id);
        
            if(!$identity || $identity['Identity']['namespace'] != $logged_in_identity['Identity']['username']) {
                # Identity not found, or identity's namespace does not match logged in username
                $this->redirect('/');
                exit;
            }
            
            $this->Session->write('Service.add.account.is_logged_in_user', true);
        }
        
        # save identity for which we want to add the servie
        # into session, so we don't need to check any further
        $this->Session->write('Service.add.account.to.identity_id', $identity['Identity']['id']);
        
        # also save, wether we add the account for a logged in user. this is
        # needed to distinguish during the process (eg no import of conacts)
        $this->Session->write('Service.add.account.is_logged_in_user', $identity['Identity']['id'] == $identity_id);
        
        if($this->data) {
            if($this->data['Account']['type'] == 1) {
                # user selected a service
                $this->Session->write('Service.add.id', $this->data['Account']['service_id']);
                $this->redirect('/'.$username.'/accounts/add/service/');
                exit;
            } else {
                # user wants to add Blog or RSS-Feed
                $this->Session->write('Service.add.id', 8); # any rss feed
                $this->redirect('/'.$username.'/accounts/add/feed/');
                exit;
            }
        }
        $this->set('services', $this->Account->Service->generateList(array('id<>8'), null, null, "{n}.Service.id", "{n}.Service.name"));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_step_2_service() {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Service.add.account.to.identity_id');
        $service_id  = $this->Session->read('Service.add.id');
        
        # check the session vars
        if(!$identity_id || !$service_id) {
            # couldn't find the session vars. so either someone skipped 
            # a step, or the user was logged out during the process
            $this->redirect('/');
            exit;
        }
        
        # reset session
        $this->Session->delete('Service.add.data');
        
        if($this->data) {
            # get title, url and preview
            $data = $this->Account->Service->getInfoFromService($service_id, $this->data['Account']['username']);    
            if(!$data) {
                $this->Account->invalidate('username', 1);
            } else {
                $this->Session->write('Service.add.data', $data);
                $this->Session->write('Service.add.type', $data['service_type_id']);
                $this->redirect('/' . $username . '/accounts/add/preview/');
                exit;
            }
        } else {
            $this->data = array('Account' => array('feed_url' => 'http://')); 
        }
         
        $this->Account->Service->recursive = 0;
        $this->Account->Service->expects('Service');
        $this->set('service', $this->Account->Service->findById($service_id));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_step_2_feed() {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Service.add.account.to.identity_id');
        
        # check the session vars
        if(!$identity_id) {
            # couldn't find the session vars. so either someone skipped 
            # a step, or the user was logged out during the process
            $this->redirect('/');
            exit;
        }
        
        # reset session
        $this->Session->delete('Service.add.data');
        
        if($this->data) {
            # get title, url and preview
            $data = $this->Account->Service->getInfoFromFeed($this->data['Account']['feed_url']);    
            if(!$data) {
                $this->Account->invalidate('feedurl', 1);
            } else {
                $data['service_type_id'] = $this->data['Account']['service_type_id'];
                $this->Session->write('Service.add.type', $data['service_type_id']);
                $this->Session->write('Service.add.data', $data);
                $this->redirect('/' . $username . '/accounts/add/preview/');
                exit;
            }
        } else {
            $this->data = array('Account' => array('feed_url' => 'http://')); 
        }
        
        $this->set('service_types', $this->Account->ServiceType->generateList(null, null, null, "{n}.ServiceType.id", "{n}.ServiceType.name"));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_step_3_preview() {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Service.add.account.to.identity_id');
        $data        = $this->Session->read('Service.add.data');
        
        # check the session vars
        if(!$identity_id || !$data) {
            # couldn't find the session vars. so either someone skipped 
            # a step, or the user was logged out during the process
            $this->redirect('/');
            exit;
        }
        
        if(isset($this->params['form'])) {
            # reset session
            $this->Session->delete('Service.add.data');
            
            if(isset($this->params['form']['submit'])) {
                # check if the acccount is not already there
                if($this->Account->findCount(array('identity_id' => $identity_id, 'feed_url' => $data['feed_url'])) == 0) {
                    # save the new account
                    $data['identity_id'] = $identity_id;
                
                    $saveable = array('identity_id', 'service_id', 'service_type_id', 
                                      'username', 'account_url', 'feed_url', 'created', 
                                      'modified');
                    $this->Account->create();
                    $this->Account->save($data);
                
                    if($this->Session->read('Service.add.account.is_logged_in_user')) {
                        # test, if we can find friends from this account
                        $contacts = $this->Account->Service->getContactsFromService($this->Account->id);
                        if(!empty($contacts)) {
                            $this->Session->write('Service.add.contacts', $contacts);
                            $this->Session->write('Service.add.account_id', $this->Account->id);
                            $this->redirect('/' . $username . '/accounts/add/friends/');
                            exit;
                        }
                    }
                }
            }
            $this->redirect('/' . $username . '/accounts/');
            exit;
        }
        $this->set('data', $data);
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_step_4_friends() {
        $username           = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id        = $this->Session->read('Service.add.account.to.identity_id');
        $logged_in_username = $this->Session->read('Identity.username');
        $service_id         = $this->Session->read('Service.add.id');
        $service_type_id    = $this->Session->read('Service.add.type');
        
        # check the session vars
        if(!$identity_id || !$logged_in_username || !$service_id || !$service_type_id) {
            # couldn't find the session vars. so either someone skipped 
            # a step, or the user was logged out during the process
            $this->redirect('/');
            exit;
        }

        if(isset($this->params['form']['cancel'])) {
            # we don't neet to go further
            $this->redirect('/' . $username . '/accounts/');
            exit;
        }
        
        if($this->data) {
            foreach($this->data as $item) {
                if(isset($item['action']) && $item['action'] > 0) {
                    # see, wether we should create a new contact, or add 
                    # a account to an existing one
                    if($item['action'] == 1) {
                        # first check, if the new identity is already there
                        $new_identity_username = $item['contactname'] . '@' . $logged_in_username;
                        $this->Account->Identity->recursive = 0;
                        $this->Account->Identity->expects('Identity');
                        $identity = $this->Account->Identity->findByUsername($new_identity_username);
                        if(!$identity) {
                            # create a new identity
                            $identity = array('is_local' => 1,
                                              'username' => $new_identity_username);
                            # saving without validation, as we have no email and no password
                            $this->Account->Identity->create();
                            if(!$this->Account->Identity->save($identity, false)) {
                                # something went wrong!
                                LogError('AccountsController::add_step_4_friends(): could not create identity "' . $identity['username'] . '"');
                                continue;
                            }
                            $new_identity_id = $this->Account->Identity->id;
                        
                            # now create the contact entry
                            $contact = array('identity_id'      => $identity_id,
                                             'with_identity_id' => $new_identity_id);
                            $this->Account->Identity->Contact->create();
                            if(!$this->Account->Identity->Contact->save($contact)) {
                                # something went wrong!
                                LogError('AccountsController::add_step_4_friends(): could not create contact');
                                continue;
                            }
                        } else {
                            # the identity already exists. we assume that the
                            # contact is there, too.
                            $new_identity_id = $identity['Identity']['id'];
                        }
                        
                        # save the new identity_id to the $item, so we can
                        # go on with adding the account
                        $item['contact'] = $new_identity_id;
                    } 
                    
                    # add account to identity specified in $item['contact']
                    $account_username = $item['username'];
                    
                    $account = array('identity_id'     => $item['contact'],
                                     'service_id'      => $service_id,
                                     'service_type_id' => $service_type_id,
                                     'username'        => $account_username,
                                     'account_url'     => $this->Account->Service->getAccountUrl($service_id, $account_username),
                                     'feed_url'        => $this->Account->Service->getFeedUrl($service_id, $account_username));
                    $this->Account->create();
                    $this->Account->save($account);
                }
            }
            # we're done!
            $this->redirect('/' . $username . '/accounts/');
            exit;
        }
        $this->Account->recursive = 1;
        $this->Account->expects('Account', 'Service');
        $this->set('account', $this->Account->findById($this->Session->read('Service.add.account_id')));

        # get data about contacts from session
        $data = $this->Session->read('Service.add.contacts');
        
        # check, if soem of these contacts already are in my local
        # database. We therefore can remove them from the list
        foreach($data as $username => $item) {
            # try to find accounts with that username first
            $this->Account->recursive = 1;
            $this->Account->expects('Account', 'Identity');
            $accounts = $this->Account->findAll(array('Account.username'        => $username,
                                                      'Account.service_id'      => $service_id,
                                                      'Account.service_type_id' => $service_type_id));
        
            # we might have several accounts found, because the same account 
            # could be stored at different local identities.
            # we also don't find those, where e. a del.icio.us RSS-Feed was
            # added, instead of a del.icio.us account directly.
            foreach($accounts as $account) {
                # now see, if the identity is local to our logged
                # in identity.
                if($account['Identity']['namespace'] == $logged_in_username) {
                    # found him/her
                    unset($data[$username]);
                    break;
                }
            }
        }
        
        # now give the data to the view
        $this->set('data', $data);
        
        $this->Account->Identity->Contact->recursive = 1;
        $this->Account->Identity->Contact->expects('Contact', 'WithIdentity');
        $data = $this->Account->Identity->Contact->findAll(array('identity_id' => $identity_id));
        $contacts = array();
        foreach($data as $item) {
            $contacts[$item['WithIdentity']['id']] = $item['WithIdentity']['username'];
        }
        $this->set('contacts', $contacts);
    }
        
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function edit($account_id) {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user
            $this->redirect('/');
            exit;
        }

        # get the account
        $this->Account->recursive = 0;
        $this->Account->expects('Account');
        $data = $this->Account->find(array('id' => $account_id, 'identity_id' => $identity_id));
        if(!$data) {
            # the account for this identity could not be found
            $this->redirect('/');
            exit;
        }
        
        if(!$this->data) {
            $this->data = $data;
        } else {
            # the form was submitted
            $this->Account->id = $account_id;
            $this->data['Account']['feedurl'] = $this->Account->Service->getFeedUrl($this->data['Account']['service_id'], $this->data['Account']['username']);
            $saveable = array('modified', 'service_id', 'username', 'feedurl');
            if($this->Account->save($this->data, true, $saveable)) {
                $this->redirect('/' . $username . '/accounts/');
                exit;
            }
        }
        
        $this->set('services', $this->Account->Service->getSelect('all'));
        $this->render('add');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function delete($account_id) {
        $username          = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id       = $this->Session->read('Identity.id');
        $identity_username = $this->Session->read('Identity.username');
        
        # check the session vars
        if(!$username || !$identity_id || !$identity_username) {
            # this user is not logged in
            $this->redirect('/');
            exit;
        }
        
        if($username != $identity_username) {
            # check, if $username belongs to the
            # logged in identities namespace
            $this->Account->Identity->recursive = 0;
            $this->Account->Identity->expects('Identity');
            $about_identity = $this->Account->Identity->findByUsername($username);
            if(!$about_identity) {
                # could not find the identity
                $this->redirect('/');
                exit;
            }
            if($about_identity['Identity']['namespace'] == $identity_username) {
                $identity_id = $about_identity['Identity']['id'];
            } else {
                # logged in user is not allowed to change something
                $this->redirect('/');
                exit;
            }
        }
        # check, wether the account belongs to the identity
        $this->Account->recursive = 0;
        $this->Account->expects('Account');
        if(1 == $this->Account->findCount(array('identity_id' => $identity_id,
                                                'id'          => $account_id))) {
            $this->Account->id = $account_id;
            $this->Account->delete();
        }
        
        $this->redirect('/' . $username . '/accounts/');
        exit;
    }
    
    /**
     * Synchronizes the given identity from another server
     * to this local NoseRub instance
     *
     * @param  string admin_hash (through $this->params)
     * @param  string username (through $this->params)
     * @return 
     * @access 
     */
    function jobs_sync() {
        $admin_hash = isset($this->params['admin_hash']) ? $this->params['admin_hash'] : '';
        $username   = isset($this->params['username'])   ? $this->Account->Identity->splitUsername($this->params['username']) : array();
        
        if($admin_hash != NOSERUB_ADMIN_HASH ||
           $admin_hash == '' ||
           empty($username) ||
           $username['domain'] == NOSERUB_DOMAIN ||
           $username['namespace'] != '') {
            # there is nothing to do for us here
            return false;
        }
        
        # see, if we can find the identity locally
        $this->Account->Identity->recursive = 0;
        $this->Account->Identity->expects('Identity');
        $identity = $this->Account->Identity->findByUsername($username['full_username']);

        if(!$identity) {
            # we could not find it, so we don't do anything
            return false;
        }
        
        return $this->Account->sync($identity['Identity']['id'], $username['url']);
    }
    
    /**
     * sync all identities with their remote server
     *
     * @param  
     * @return 
     * @access 
     */
    function jobs_sync_all() {
        $admin_hash = isset($this->params['admin_hash']) ? $this->params['admin_hash'] : '';
        
        if($admin_hash != NOSERUB_ADMIN_HASH ||
           $admin_hash == '') {
            # there is nothing to do for us here
            return false;
        }
        
        # get all not local identities
        $this->Account->Identity->recursive = 0;
        $this->Account->Identity->expects('Identity');
        $identities = $this->Account->Identity->findAll(array('is_local' => 0), null, 'last_sync ASC');
        foreach($identities as $identity) {
            if($identity['Identity']['domain']    != NOSERUB_DOMAIN &&
               $identity['Identity']['namespace'] == '' &&
               $identity['Identity']['url']       != '') {
                $this->Account->sync($identity['Identity']['id'], $identity['Identity']['url']);       
            }
        }

        return true;
    }
}