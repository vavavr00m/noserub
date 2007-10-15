<?php
 
class SyndicationsController extends AppController {
    var $uses = array('Syndication');
    var $helpers = array('form', 'html');
    
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
            echo '<pre>'; print_r($this->data); echo '</pre>';
            exit;
        }
        
        # get all accounts from this user, that have feeds
        $this->Syndication->Account->recursive = 1;
        $this->Syndication->Account->expects('Account', 'Service');
        $this->set('accounts', $this->Syndication->Account->findAll(array('Account.identity_id' => $session_identity['id'],
                                                                          'Account.feed_url <> ""')));

        # get all accounts from this users contacts
        $this->Syndication->Identity->Contact->recursive = 3;
        $this->Syndication->Identity->Contact->expects('Contact.WithIdentity', 
                                                       'WithIdentity.Account.Service');
        $this->set('contacts', $this->Syndication->Identity->Contact->findAllByIdentityId($session_identity['id']));
        
        $this->set('headline', 'Add new Feed');
    }
}