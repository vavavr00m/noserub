<?php

class WidgetsController extends AppController {
    public $components = array('cluster');
    public $helpers = array('nicetime');
        
    /**
     * Various elements / pages
     */
     
    public function navigation() {
        $this->loadModel('Identity');
         
        if(Context::isLoggedInIdentity()) {
            $this->Identity->id = Context::loggedInIdentityId();
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
 	    if(!Context::isLoggedInIdentity()) {
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
 	    if(Context::isAdmin()) {
 	        $this->loadModel('Network');
 	        $this->retrieveFormErrors('Network');
 	        if(!$this->data) {
         	    $this->data = $this->Network->find('first', array(
         	        'contain' => false,
         	        'conditions' => array(
         	            'Network.id' => Context::NetworkId()
         	        )
         	    ));
 	        }
        }
 	}
 	
 	public function form_admin_password() {
 	    if(Context::isAdmin()) {
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
 	    if($identity_id == Context::loggedInIdentityId()) {
 	        $data = Context::isLoggedInIdentity();
 	    } else {
 	        $data = Context::read('identity');
 	    }
 	    
 	    $this->set('data', $data);
 	} 
 	
 	/**
 	 * Entries
 	 */
 	
 	/**
 	 * Form to add an entry
 	 */ 
 	public function form_add_entry() {
 	    $this->loadModel('ServiceType');
	    $filters = $this->ServiceType->getFilters();
 	    unset($filters['audio']);
 	    unset($filters['noserub']);
 	    unset($filters['video']);
 	    unset($filters['document']);
 	    $this->set('filters', $filters);
 	}
 	
 	/**
 	 * Display an entry with all comments and favorites
 	 */
 	public function view_entry() {
 	    $this->loadModel('Entry');
 	    
        $data = $this->Entry->find('first', array(
            'contain' => array(
                'Identity', 'Account', 'ServiceType',
                'FavoritedBy', 'Comment'
            ),
            'conditions' => array(
                'Entry.id' => Context::entryId()
        )));
        
        if($data) {
            $data = $this->Entry->Identity->addIdentity('FavoritedBy', $data);
            $data = $this->Entry->Identity->addIdentity('Comment', $data);
        
            # go through all favorites. if it's the current identity, set a marker
            foreach($data['FavoritedBy'] as $item) {
                if(Context::loggedInIdentityId() == $item['identity_id']) {
                    $this->set('already_marked', true);
                    break;
                }
            }
        }
        $this->set('data', $data);
 	}
 	
 	/**
 	 * Comments
 	 */
 	 
 	/**
 	 * form to add a comment to an entry
 	 */
 	public function form_add_comment() {
 	    
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
        $this->set('communications', $this->Account->get($identity_id, 'communication'));
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
 	
 	public function group_info() {
 	    if(Context::groupId()) {
            $this->loadModel('Group');
 	        $this->set('group_info', $this->Group->find('first', array(
 	            
 	            'conditions' => array(
 	                'Group.id' => Context::groupId()
 	            )
 	        )));
 	    }
 	}
 	
 	public function form_groups_add() {
 	}
 	
 	public function group_overview() {
 	    if(Context::groupId()) {
 	        $this->loadModel('Entry');
     	    $this->set('data', $this->Entry->find('all', array(
                'contain' => false,
                'conditions' => array(
                    'Entry.group_id' => Context::groupId()
                ),
                'order' => 'Entry.published_on DESC',
                'limit' => 10
            )));
        }
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
 	    if(Context::isSelf()) {
 	        $this->loadModel('Contact');
 	    
 	        $this->set('contact_tags', $this->Contact->getTagList(Context::loggedInIdentityId()));
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
        if(Context::isSelf()) {
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
            if(count($contacts) < 9) {
                $contacts[] = $contact;
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
            $data = $this->Contact->getForDisplay(Context::loggedInIdentityId(), array('tag' => 'all'));
        
            # we need to go through all this now and get Accounts and Services
            # also save all contacts
            $contacts = array();
            foreach($data as $key => $value) {
                $contacts[] = $value['WithIdentity'];
            }

            $contact_ids = Set::extract($contacts, '{n}.id');
            $contact_ids[] = Context::loggedInIdentityId();
        } else {
            $contact_ids = array(Context::read('identity.id'));
        }
        # get last 50 items
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
        $this->loadModel('Entry');
    
        $items = $this->Entry->getForDisplay(
            array(
                'filter' => array('photo'),
                'identity_id' => $this->getIdentityId()
            ),
            5, 
            true
        );
        if($items) {
            usort($items, 'sort_items');
        }
    
        $this->set('data', $items);    
    }
    
    /**
     * Settings
     */
     
    public function form_accounts() {
        $this->loadModel('Account');
        $this->retrieveFormErrors('Account');
        $this->set('data', $this->Account->get(Context::loggedInIdentityId()));
        $services = $this->Account->Service->find('list', array('order' => 'name'));
        unset($services[8]);
        $this->set('services', $services);
        $this->set('service_types', $this->Account->ServiceType->find('list'));
    }
    
    public function form_account_edit() {
        $this->loadModel('Account');
        $this->retrieveFormErrors('Account');
        $params = Context::read('params.named');
        $account_id = $params['id'];
        $this->data = $this->Account->find(
            'first',
            array(
                'conditions' => array(
                    'Account.id' => $account_id,
                    'Account.identity_id' => Context::loggedInIdentityId()
                )
            )
        );
        $services = $this->Account->Service->find('list', array('order' => 'name'));
        $this->set('services', $services);
        $this->set('service_types', $this->Account->ServiceType->find('list'));
    }
    
    public function account_settings_twitter() {
        
    }
    
    public function form_locations() {
        $this->loadModel('Location');
        $this->retrieveFormErrors('Location');
        $this->set('data', $this->Location->get(Context::loggedInIdentityId()));
    }
    
    public function form_locations_edit() {
        $this->loadModel('Location');
        $this->retrieveFormErrors('Location');
        $params = Context::read('params.named');
        $location_id = $params['id'];
        $this->data = $this->Location->find(
            'first',
            array(
                'conditions' => array(
                    'Location.id' => $location_id,
                    'Location.identity_id' => Context::loggedInIdentityId()
                )
            )
        );
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
            } else if(Context::isLoggedInIdentity()) {
                # if not, use the logged in identity
                $identity_id = Context::loggedInIdentityId();
            }
         }
     
        return $identity_id;
     }
}