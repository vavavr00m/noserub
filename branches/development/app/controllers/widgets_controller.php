<?php

class WidgetsController extends AppController {
    
    public $components = array('cluster');
    public $helpers = array('nicetime');
       
    private $generic_methods = array(
        'admin_navigation', 'admin_login'
    );
        
    /**
     * Various elements / pages
     */
     
    public function navigation() {
        $this->loadModel('Identity');
         
        if(Context::read('logged_in_identity')) {
            $this->Identity->id = Context::read('logged_in_identity.id');
        } else {
            $this->Identity->id = false;
        }
        $this->set('groups', $this->Identity->getSubscribedGroups());
        $this->set('networks', $this->Identity->getSubscribedNetworks());
        
        $type = isset($this->params['type']) ? $this->params['type'] : 'main';
        $this->render($type . '_navigation');
 	}
 	
 	/**
 	 * Admin
 	 */
 	 
 	public function admin_navigation() {
 	}
 	
 	public function admin_login() {
 	    if(!Context::read('logged_in_identity')) {
 	        # but if there is no identity yet, to which you could log in...
            $this->loadModel('Identity');
            if(!$this->Identity->isIdentityAvailableForLogin()) {
                Context::write('show_admin_login', true);
            } else {
                Context::write('show_admin_login', false);
            }
        }
 	}
 	
 	public function form_admin_settings() {
 	    if(Context::read('admin_id')) {
 	        $this->loadModel('Network');
 	        $this->retrieveFormErrors('Network');
 	        if(!$this->data) {
         	    $this->data = $this->Network->find('first', array(
         	        'contain' => false,
         	        'conditions' => array(
         	            'Network.id' => Context::read('network.id')
         	        )
         	    ));
 	        }
        }
 	}
 	
 	public function form_admin_password() {
 	    if(Context::read('admin_id')) {
 	        $this->loadModel('Admin');
 	        $this->retrieveFormErrors('Admin');
 	        if(!$this->data) {
         	    $this->data = $this->Admin->find('first', array(
         	        'contain' => false,
         	        'conditions' => array(
         	            'Admin.id' => Context::read('admin_id')
         	        )
         	    ));
 	        }
 	        
 	        # remove the password field, else it would be displayed
 	        # in the form (the MD5 version)
 	        $this->data['Admin']['password'] = '';
        }
 	}
 	
 	/**
 	 * Profiles
 	 */
 	
 	public function profile() {
 	    $identity_id = $this->getIdentityId();
 	    if($identity_id == Context::read('logged_in_identity.id')) {
 	        $data = Context::read('logged_in_identity');
 	    } else {
 	        $data = Context::read('identity');
 	    }
 	    
 	    $this->set('data', $data);
 	} 
 	
 	/**
 	 * User Settings
 	 */
 	 
 	public function settings_navigation() {
 	}
 	
 	/**
 	 * Accounts
 	 */
 	
 	public function accounts() {
 	    $this->loadModel('Account');
 	    
 	    $identity_id = $this->getIdentityId();
 	    $this->set('accounts', $this->Account->get($identity_id, 'web'));
 	} 
 	
 	/**
 	 * Communications
 	 */
 	public function communications() {
 	    $this->loadModel('Account');
 	    
 	    $identity_id = $this->getIdentityId();
        $this->set('communications', $this->Account->get($identity_id, 'contact'));
 	} 
 	
 	/**
 	 * Networks
 	 */
 	
 	/**
 	 * display networks that this user subscribed to
 	 */
 	public function networks() {
        $this->loadModel('Identity');
 	     
 	    $this->Identity->id = $this->getIdentityId();
 	    $this->set('networks', $this->Identity->getSubscribedNetworks());
 	}
 	
 	/**
 	 * display a form to manage networks
 	 */
    public function form_networks() {
        $this->loadModel('Network');
        
        $this->set('networks', $this->Network->getSubscribable());        
 	}
 	
 	/**
 	 * Groups
 	 */
 	
 	/**
 	 * display groups that this user subscribed to
 	 */
 	public function groups() {
        $this->loadModel('Identity');
 	     
 	    $this->Identity->id = $this->getIdentityId();
 	    $this->set('groups', $this->Identity->getSubscribedGroups());
 	}
 	
 	public function groups_overview() {
 	    $this->loadModel('Group');
 	    $this->set('groups', $this->Group->getOverview());
 	}
 	
 	public function new_groups() {
 	    $this->loadModel('Group');
 	    $this->set('groups', $this->Group->getNew());
 	}
 	
 	public function popular_groups() {
 	    $this->loadModel('Group');
 	    $this->set('groups', $this->Group->getPopular());
 	}
 	
 	/**
 	 * Filters
 	 */
 	 
 	public function contact_filter() {
 	    if(Context::read('is_self')) {
 	        $this->loadModel('Contact');
 	    
 	        $this->set('contact_tags', $this->Contact->getTagList(Context::read('logged_in_identity.id')));
        } else {
            return false;
        }
 	}
 	
    /**
     * Identities / Users
     */
    
    public function new_users() {
        $this->loadModel('Identity');
	    $this->set('data', $this->Identity->getNewbies(9));
	}
	
	public function popular_users() {
	    $this->loadModel('Identity');
	    $this->set('data', $this->Identity->getPopular(9));
	}

	public function last_active_users() {
	    $this->loadModel('Identity');
	    $this->set('data', $this->Identity->getLastActive(9));
	}
	
	public function last_logged_in_users() {
	    $this->loadModel('Identity');
	    $this->set('data', $this->Identity->getLastLoggedIn(9));
	}
	
	/**
	 * Contacts
	 */

    /**
     * contacts of a given identity.
     */
    public function contacts_for_identity() {
        $this->loadModel('Contact');
         
        $identity_id = $this->getIdentityId();
        $this->Contact->Identity->contain();
        $this->Contact->Identity->id = $identity_id;
        $this->set('identity', $this->Contact->Identity->read());
        
        $tag_filter = $this->Session->read('Filter.Contact.Tag');
        if(!$tag_filter) {
            $tag_filter = 'all';
        }
        
        # get (filtered) contacts
        if(Context::read('is_self')) {
            # this is my network, so I can show every contact
            $contact_filter = array('tag' => $tag_filter);
        } else {
            # this is someone elses network, so I show only the noserub contacts
            $contact_filter = array('tag' => $tag_filter, 'type' => 'public');
        }
        
        $layout = isset($this->params['layout']) ? $this->params['layout'] : '';
        if($layout == 'list') {
            $limit = null;
        } else {
            $limit = 9;
        }
        
        $data = $this->Contact->getForDisplay($identity_id, $contact_filter, $limit);
        $this->set('data', $data);
        /*
        $contacts = array();
        foreach($data as $key => $value) {
            $contacts[] = $value['WithIdentity'];
        }
        $this->set('data', $contacts);
        */
        if($layout == 'list') {
            $this->render('contacts_list');
        } else {
            $this->render('contacts_box');
        }
     }

     public function my_contacts() {
        $this->loadModel('Contact');
        
        $layout = isset($this->params['layout']) ? $this->params['layout'] : '';
        if($layout == 'list') {
            $limit = null;
        } else {
            $limit = 9;
        }
        
        $logged_in_identity_id = $this->Session->read('Identity.id');
        if(!$logged_in_identity_id) {
            return false;
        }
        
        # get contacts of the displayed profile
        $all_contacts = $this->Contact->getForIdentity($logged_in_identity_id, array(), $limit);
        
        $contacts = array();
        foreach($all_contacts as $contact) {
            if(strpos($contact['WithIdentity']['username'], '@') === false) {
                if(count($contacts) < 9) {
                    $contacts[] = $contact;
                }
            } else {
                if(count($contacts) < 9) {
                    $contacts[] = $contact;
                }
            }
        }
        $this->set('data', $contacts);
        
        if($layout == 'list') {
            $this->render('contacts_list');
        } else {
            $this->render('contacts_box');
        }
     }
     
    /**
     * Lifestream
     **/
     
    public function lifestream() {
        $this->loadModel('Contact');
        
        $type = $this->params['type'];

        $show_in_overview = $this->Contact->Identity->Account->ServiceType->getDefaultFilters();
        $filter = $show_in_overview;

        if($type == 'network') {
            $data = $this->Contact->getForDisplay(Context::read('logged_in_identity.id'), array('tag' => 'all'));
        
            # we need to go through all this now and get Accounts and Services
            # also save all contacts
            $contacts = array();
            foreach($data as $key => $value) {
                $contacts[] = $value['WithIdentity'];
            }

            $contact_ids = Set::extract($contacts, '{n}.id');
            $contact_ids[] = Context::read('logged_in_identity.id');
        } else {
            $contact_ids = array(Context::read('identity.id'));
        }
        # get last 100 items
        $conditions = array(
            'filter'      => $filter,
            'identity_id' => $contact_ids
        );
        $items = $this->Contact->Identity->Entry->getForDisplay($conditions, 50, true);
        if($items) {
            usort($items, 'sort_items');
            $items = $this->cluster->removeDuplicates($items);
            $items = $this->cluster->create($items);
        }
    
        $this->set('data', $items);
    }
    
    public function photos() {
        
    }
    
    /**
     * private methods
     */
      
    /**
     * If no identity_id was given by param, we're looking
     * for the identity we currently look at. if none
     * is found, the logged in identity is used
     *
     * @return $int
     */
    private function getIdentityId() {
        $identity_id = isset($this->params['identity_id']) ? $this->params['identity_id'] : 0;
        if(!$identity_id) {
            # if no identity_id was given in the params, use the one
            # from the current page
            if(Context::read('identity')) {
                $identity_id = Context::read('identity.id');
            } else if(Context::read('logged_in_identity')) {
                # if not, use the logged in identity
                $identity_id = Context::read('logged_in_identity.id');
            }
         }
     
        return $identity_id;
     }
}