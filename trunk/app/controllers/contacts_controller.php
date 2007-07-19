<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    var $uses = array('Contact');
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
        
        $this->Contact->recursive = 1;
        $this->Contact->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity');
        $this->set('data', $this->Contact->findAllByIdentityId($identity_id));
    }
    
    /**
     * adds a new contact to an identity
     * todo: check for existing identities
     *
     * @param  
     * @return 
     * @access 
     */
    function add() {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user
            $this->redirect('/');
            exit;
        }
        
        if($this->data) {
            $this->Contact->data = $this->data;
            if($this->Contact->validates()) {
                # we now need to create a new identity and a new contact
                # create the username with the special namespace
                $identity_username = $this->data['Contact']['username'] . ':' . $username;
                # check, if this is unique
                $this->Contact->Identity->recursive = 0;
                $this->Contact->Identity->expects('Contact');
                if($this->Contact->Identity->findCount(array('username' => $identity_username)) == 0) {
                    $this->Contact->Identity->create();
                    $identity = array('username' => $identity_username);
                    $saveable = array('username');
                    # no validation, as we have no password.
                    if($this->Contact->Identity->save($identity, false, $saveable)) {
                        # create the contact now
                        $this->Contact->create();
                        $contact = array('identity_id'      => $identity_id,
                                         'with_identity_id' => $this->Contact->Identity->id);
                        $saveable = array('identity_id', 'with_identity_id', 'created', 'modified');
                        if($this->Contact->save($contact, true, $saveable)) {
                            $this->redirect('/noserub/' . $username . '/contacts/');
                            exit;
                        }
                    }
                }
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
    function network() {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user
            $this->redirect('/');
            exit;
        }
        
        # get all contact identities and their services
        $this->Contact->recursive = 3;
        $this->Contact->expects('Contact.WithIdentity', 
                                'WithIdentity.WithIdentity','WithIdentity.Account',
                                'Account.Account', 'Account.Service',
                                'Service.Service');
        $data = $this->Contact->findAllByIdentityId($identity_id);
        
        # now get all feedurls
        $feedurls = array();
        foreach($data as $contact) {
            foreach($contact['WithIdentity']['Account'] as $account) {
                $feedurls[] = $account['feedurl'];
            }
        }
        
        # get all data from the feeds
        vendor('simplepie/simplepie');
        $first_items = array();

        // Let's go through the array, feed by feed, and store the items we want.
        foreach ($feedurls as $url) {
            // Use the long syntax
            $feed = new SimplePie();
            $feed->set_feed_url($url);
            $feed->init();

        	// How many items per feed should we try to grab?
        	$items_per_feed = 5;

        	// As long as we're not trying to grab more items than the feed has, go through them one by one and add them to the array.
        	for($x = 0; $x < $feed->get_item_quantity($items_per_feed); $x++) {
        		$first_items[] = $feed->get_item($x);
        	}

            // We're done with this feed, so let's release some memory.
            unset($feed);
        }
        
        

        // Now we can sort $first_items with our custom sorting function.
        usort($first_items, "sort_items");
        
        
        $this->set('data', $first_items);
    }
}
function sort_items($a, $b) {
	return SimplePie::sort_items($a, $b);
}