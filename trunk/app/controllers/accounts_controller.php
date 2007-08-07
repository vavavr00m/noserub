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
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user. for the moment, all identities are privat
            $this->redirect('/');
            exit;
        }
        
        $this->Account->recursive = 1;
        $this->Account->expects('Account.Account', 'Account.Service', 'Service.ervice');
        $this->set('data', $this->Account->findAllByIdentity_id($identity_id));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add($with_identity_id = null) {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user
            $this->redirect('/');
            exit;
        }

        if($with_identity_id !== null) {
            $this->Session->delete('add_account_with_identity_id');
            # test, if the logged in identity has this
            # with_identity_id as contact
            $this->Account->Identity->Contact->recursive = 0;
            $this->Account->Identity->Contact->expects = array('Contact');
            if(1 != $this->Account->Identity->Contact->findCount(array('identity_id'      => $identity_id,
                                                                       'with_identity_id' => $with_identity_id))) {
                # someone is trying to be nasty
                #$this->redirect('/');
                exit;
            }
            $this->Account->Identity->recursive = 0;
            $this->Account->Identity->expects = array('Identity');
            $this->set('with_identity', $this->Account->Identity->findById($with_identity_id));
            $this->Session->write('add_account_with_identity_id', $with_identity_id);
        }
        if($this->data) {
            $this->Account->create();
            $saveable = array('identity_id', 'service_id', 'username', 'account_url', 'feed_url', 'created', 'modified');
            $with_identity_id = $this->Session->read('add_with_identity_id');
            if($this->Session->check('add_account_with_identity_id')) {
                # create account for contact identity
                $this->data['Account']['identity_id'] = $this->Session->read('add_account_with_identity_id');
                $this->Session->delete('add_account_with_identity_id');
            } else {
                # create account for logged in identity
                $this->data['Account']['identity_id'] = $identity_id;
            }
            if($this->data['Account']['service_id'] != 7) {
                # only look into getting the feed, when service is not "blog"
                $service_id = $this->data['Account']['service_id'];
                $username   = $this->data['Account']['username'];
                $this->data['Account']['feed_url']    = $this->Account->Service->getFeedUrl($service_id, $username);
                $this->data['Account']['account_url'] = $this->Account->Service->getAccountUrl($service_id, $username);
            }
            if($this->Account->save($this->data, true, $saveable)) {
                $this->redirect('/noserub/' . $username . '/accounts/');
                exit;
            }
        }
        $this->set('services', $this->Account->Service->getSelect('all'));
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
            $this->data['Account']['feedurl'] = $this->Account->Service->username2feed($this->data['Account']['username'], $this->data['Account']['service_id']);
            $saveable = array('modified', 'service_id', 'username', 'feedurl');
            if($this->Account->save($this->data, true, $saveable)) {
                $this->redirect('/noserub/' . $username . '/accounts/');
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
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user
            $this->redirect('/');
            exit;
        }
        
        # check, wether the account belongs to the identity
        $this->Account->recursive = 0;
        $this->Account->expects('Account');
        if(1 == $this->Account->findCount(array('identity_id' => $identity_id,
                                                'id'          => $account_id))) {
            $this->Account->id = $account_id;
            $this->Account->delete();
        }
        
        $this->redirect('/noserub/' . $username . '/accounts/');
        exit;
    }
}