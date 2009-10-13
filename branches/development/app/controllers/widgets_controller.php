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
        $this->set('locations', $this->Identity->Location->getNew($this->Identity->id));
        $this->set('events', $this->Identity->Event->getNew($this->Identity->id));
        
        $type = isset($this->params['type']) ? $this->params['type'] : 'main';
        $this->render($type . '_navigation');
 	}
 	
 	public function meta_keywords() {
 	    if(Context::isPage('profile.home')) {
 	        $this->set('keywords', Context::read('identity.keywords'));
 	    } 
 	}
 	
 	public function meta_description() {
 	    if(Context::isPage('profile.home')) {
 	        $this->set('description', Context::read('identity.about'));
 	    } 
 	}
 	
 	public function search() {
 	    $q = isset($this->params['url']['q']) ? $this->params['url']['q'] : '';
        $q = strtolower(htmlspecialchars(strip_tags($q), ENT_QUOTES, 'UTF-8'));
        
        $this->set('q', $q);
    }
    
    public function search_results() {
        $this->loadModel('Entry');
 	    
        $q = isset($this->params['url']['q']) ? $this->params['url']['q'] : '';
        $q = strtolower(htmlspecialchars(strip_tags($q), ENT_QUOTES, 'UTF-8'));
        
        if($q) {
            $conditions = array(
                'search'      => $q
            );
            $items = $this->Entry->getForDisplay($conditions, 50);
            usort($items, 'sort_items');
            $items = $this->cluster->removeDuplicates($items);
            $items = $this->cluster->create($items);
        } else {
            $items = array();
        }
        
    	$this->set('items', $items);
    	$this->set('q', $q);
    }
    
    public function map() {
        
    }
    
    public function logged_in_user() {
        
    }
    
    public function ad() {
        $this->loadModel('Ad');
        $name = isset($this->params[0]) ? $this->params[0] : '';
        if($name) {
            $this->set('ad_content', $this->Ad->getAd($name));
        }
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
 	
 	public function form_network_settings() {
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
 	
 	public function form_ad_management() {
 	    if(Context::isAdmin()) {
 	        $this->loadModel('Ad');
 	        $this->retrieveFormErrors('Ad');
            $this->set('adspots', $this->Ad->getForTheme());
            $ads = $this->Ad->find('all', array(
                'contain' => false,
                'conditions' => array(
                    'network_id' => Context::networkId()
            )));
            $this->set('ads', $ads);
            $ad_list = array();
            foreach($ads as $item) {
                $ad_list[$item['Ad']['id']] = $item['Ad']['name'] . ' (' . $item['Ad']['width'] . 'x' . $item['Ad']['height'] . ')';
            }
            $ad_list[0] = __('No Ad', true);
            $this->set('ad_list', $ad_list);
            $this->set('assignments', $this->Ad->getAssignmentsForTheme());
 	    }
 	}
 	
  	/**
 	 * Profiles
 	 */
 	
 	public function profile() {
 	    $identity_id = $this->getIdentityId();
 	    if($identity_id == Context::loggedInIdentityId()) {
 	        // load the identity from the database, so that
 	        // we get all the latest changes
 	        $this->loadModel('Identity');
 	        $data = $this->Identity->find('first', array(
 	            'contain' => false,
 	            'conditions' => array(
 	                'Identity.id' => Context::loggedInIdentityId()
 	            )
 	        ));
 	        $data = $data['Identity'];
 	    } else {
 	        $data = Context::read('identity');
 	    }
 	    
 	    $this->set('data', $data);
 	} 
 	
 	/**
 	 * Messages
 	 */
 	
 	public function messages_navigation() {
 	    
 	}
 	 
 	public function unread_messages() {
 	    
 	}

 	public function messages() {
 	    if(Context::isLoggedIn()) {
 	        $this->loadModel('Message');
     	    $folder = isset($this->params[0]) ? strtolower($this->params[0]) : 'inbox';
            if(!$this->Message->isValidFolder($folder)) {
                $folder = 'inbox';
            }
        
            $this->set('folder', $folder);
        
            $this->set('data', $this->Message->find('all', array(
                'contain' => false,
                'conditions' => array(
                    'identity_id' => Context::loggedInIdentityId(),
                    'folder' => $folder
                ),
                'order' => 'created DESC'
            )));
        }
 	}
 	
 	public function view_message() {
 	    $message_id = Context::messageId();
 	    
 	    if(Context::isLoggedIn() && $message_id) {
 	        $this->loadModel('Message');
 	        $data = $this->Message->find('first', array(
 	            'contain' => false,
 	            'conditions' => array(
 	                'id' => $message_id,
 	                'identity_id' => Context::loggedInIdentityId()
 	            )
 	        ));
 	        if($data && !$data['Message']['read']) {
 	            $this->Message->id = $message_id;
 	            $this->Message->saveField('read', 1);
 	        }
 	        $this->set('data', $data); 
 	    }
 	}
 	
 	public function form_reply_message() {
 	    $message_id = Context::messageId();
 	    
 	    if(Context::isLoggedIn() && $message_id) {
 	        $this->loadModel('Message');
 	        $data = $this->Message->find('first', array(
 	            'contain' => false,
 	            'conditions' => array(
 	                'id' => $message_id,
 	                'identity_id' => Context::loggedInIdentityId()
 	            )
 	        ));
 	        $this->data = $this->Message->reply($data, Context::loggedInIdentityId());
 	        
 	        $this->render('form_add_message');
 	    }
 	}
 	
 	public function form_add_message() {
 	    if(Context::isLoggedIn()) {
 	        $to_identity_id = Context::read('message_to_identity_id');
            if($to_identity_id) {
                $this->loadModel('Identity');
                $this->Identity->id = $to_identity_id;
                $this->data = array(
                    'Message' => array(
                        'to_from' => $this->Identity->field('username')
                ));
            }
 	        $this->render('form_add_message');
 	    }
 	}

 	/**
 	 * Entries
 	 */
 	
 	/**
 	 * Form to add an entry
 	 */ 
 	public function form_add_entry() {
	    $filters = $this->ServiceType->getFilters();
 	    unset($filters['audio']);
 	    unset($filters['noserub']);
 	    unset($filters['video']);
 	    unset($filters['document']);
 	    unset($filters['event']);
 	    unset($filters['location']);
 	    $this->set('filters', $filters);
 	}
 	
 	/**
 	 * Display an entry with all comments and favorites
 	 */
 	public function view_entry() {
 	    $this->loadModel('Entry');
 	    
        $data = $this->Entry->find('first', array(
            'contain' => array(
                'Identity', 'Account',
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
     * Displays the header for a specific group
     */
 	public function group_head() {
 	    
 	}
 	
 	/**
 	 * Show nine members of the group
 	 */
 	public function group_members() {
 	    if(Context::groupId()) {
 	        $this->loadModel('Group');
 	        $this->set('data', $this->Group->getMembers(9));
 	    }
 	}
 	
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
 	            'contain' => false,
 	            'conditions' => array(
 	                'Group.id' => Context::groupId()
 	            )
 	        )));
 	    }
 	}
 	
 	public function group_statistics() {
 	    if(Context::groupId()) {
            $this->loadModel('Group');
 	        $this->set('group_statistics', $this->Group->find('first', array(
 	            'contain' => array('GroupMaintainer'),
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
                    'Entry.foreign_key' => Context::groupId(),
                    'Entry.model' => 'group'
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
 	 * Locations
 	 */
 	
 	/**
     * Displays the header for a specific location
     */
 	public function location_head() {
 	    
 	}
 	
 	public function form_add_location() {
 	    $this->loadModel('Location');
 	    $this->set('types', $this->Location->getTypes());
 	} 
 	
 	public function location_info() {
 	    if(Context::locationId()) {
            $this->loadModel('Location');
 	        $this->set('location_info', $this->Location->find('first', array(
 	            'contain' => array('Identity'),
 	            'conditions' => array(
 	                'Location.id' => Context::locationId()
 	            )
 	        )));
 	        $this->set('types', $this->Location->getTypes());
 	    }
 	}
 	
 	public function location_overview() {
 	    if(Context::locationId()) {
 	        $this->loadModel('Entry');
     	    $this->set('data', $this->Entry->find('all', array(
                'contain' => false,
                'conditions' => array(
                    'Entry.foreign_key' => Context::locationId(),
                    'Entry.model' => 'location'
                ),
                'order' => 'Entry.published_on DESC',
                'limit' => 10
            )));
        }
 	}
 	
 	/**
 	 * Events
 	 */
 	
 	/**
     * Displays the header for a specific event
     */
 	public function event_head() {
 	    
 	}
 	
 	public function form_add_event() {
 	    if(Context::isLoggedIn()) {
     	    $this->loadModel('Event');
     	    $this->set('types', $this->Event->getTypes());
     	    $locations = $this->Event->Location->getNew(Context::loggedInIdentityId(), 5, 'list');
     	    $locations[0] = __('Will specify location later', true);
     	    $this->set('locations', $locations);
 	    }
 	}
 	
 	public function event_info() {
 	    if(Context::eventId()) {
            $this->loadModel('Event');
 	        $this->set('event_info', $this->Event->find('first', array(
 	            'contain' => array(
 	                'Identity', 
 	                'Location'
 	            ),
 	            'conditions' => array(
 	                'Event.id' => Context::eventId()
 	            )
 	        )));
 	        $this->set('types', $this->Event->getTypes());
 	    }
 	}
 	
 	public function event_overview() {
 	    if(Context::eventId()) {
 	        $this->loadModel('Entry');
     	    $this->set('data', $this->Entry->find('all', array(
                'contain' => false,
                'conditions' => array(
                    'Entry.foreign_key' => Context::eventId(),
                    'Entry.model' => 'event'
                ),
                'order' => 'Entry.published_on DESC',
                'limit' => 10
            )));
        }
 	}
 	  
 	public function last_events() {
 	    $this->loadModel('Event');
 	    $this->set('data', $this->Event->find('all', array(
 	        'contain' => false,
 	        'conditions' => array(
 	            'Event.to_datetime < NOW()'
 	        ),
 	        'order to_datetime DESC',
 	        'limit 5'
 	    )));
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
 	
 	public function entry_filter() {
        $this->set('service_types', $this->ServiceType->getFilters());
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
        
        $rel_filter = Context::contactFilter();
        if(!$rel_filter) {
            $rel_filter = array();
        }
        
        # get (filtered) contacts
        if(Context::isSelf()) {
            # get contacts of the displayed profile
            $rel_filter = Context::contactFilter();
            if($rel_filter) {
                $rel_filter_private = $this->Contact->ContactType->arrayToFilter($identity_id, $rel_filter);
                $rel_filter_noserub = $this->Contact->NoserubContactType->arrayToFilter($rel_filter);
                $rel_filter = array_merge($rel_filter_private, $rel_filter_noserub);
            }
            # this is my network, so I can show every contact
            $contact_filter = array('tag' => $rel_filter);
        } else {
            # this is someone elses network, so I show only the noserub contacts
            $contact_filter = array('tag' => $rel_filter, 'type' => 'public');
        }
        
        $layout = isset($this->params['layout']) ? $this->params['layout'] : '';
        if($layout == 'list') {
            $limit = null;
        } else {
            $limit = 9;
        }
        
        $data = $this->Contact->getForDisplay($identity_id, $contact_filter, $limit);
        $this->set('data', $data);
        
        if($layout == 'rdf') {
            $this->render('contacts_rdf');
        } else if($layout == 'list') {
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
        $rel_filter = Context::contactFilter();
        if($rel_filter) {
            $rel_filter_private = $this->Contact->ContactType->arrayToFilter($logged_in_identity_id, $rel_filter);
            $rel_filter_noserub = $this->Contact->NoserubContactType->arrayToFilter($rel_filter);
            $rel_filter = array_merge($rel_filter_private, $rel_filter_noserub);
        }
        
        $all_contacts = $this->Contact->getForIdentity($logged_in_identity_id, $rel_filter, $limit);
        
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

        $filter = Context::entryFilter();
        if(!$filter) {
            $filter = $this->ServiceType->getDefaultFilters();
        }
        
        $with_restricted = true;
        if(Context::isHome()) {
            # type will then always be network
            $contact_ids = false;
            $with_restricted = false;
        } else if($type == 'network') {
            $rel_filter = Context::contactFilter();
            if($rel_filter == array('me')) {
                $contact_ids = array(Context::loggedInIdentityId());
            } else {
                if($rel_filter) {
                    $logged_in_identity_id = Context::loggedInIdentityId();
                    $rel_filter_private = $this->Contact->ContactType->arrayToFilter($logged_in_identity_id, $rel_filter);
                    $rel_filter_noserub = $this->Contact->NoserubContactType->arrayToFilter($rel_filter);
                    $filter = array('tag' => array_merge($rel_filter_private, $rel_filter_noserub));
                }
                $data = $this->Contact->getForDisplay(Context::loggedInIdentityId(), $filter);

                # we need to go through all this now and get Accounts and Services
                # also save all contacts
                $contacts = array();
                foreach($data as $key => $value) {
                    $contacts[] = $value['WithIdentity'];
                }

                $contact_ids = Set::extract($contacts, '{n}.id');
                if(!$rel_filter || in_array('me', $rel_filter)) {
                    $contact_ids[] = Context::loggedInIdentityId();
                }
            }
        } else {
            $contact_ids = array(Context::read('identity.id'));
        }
        # get last 50 items
        $conditions = array('filter' => $filter);
        if($contact_ids) {
            $conditions['identity_id'] = $contact_ids;
        }
        $items = $this->Contact->Identity->Entry->getForDisplay($conditions, 50, $with_restricted);
        if($items) {
            usort($items, 'sort_items');
            $items = $this->cluster->removeDuplicates($items);
            $items = $this->cluster->create($items);
        }
    
        $this->set('data', $items);
    }
    
    public function photos() {
        $this->loadModel('Entry');
    
        $filter = array(
            'filter' => array('photo')
        );
        $identity_id = $this->getIdentityId();
        if($this->getIdentityId()) {
            $filter['identity_id'] = $identity_id;
        }
        
        if(Context::groupId()) {
            // we are on a group page, so just show photos
            // for this group
            $filter['group_id'] = Context::groupId();
        }
        if(Context::locationId()) {
            // we are on a location page, so just show photos
            // for this location
            $filter['location_id'] = Context::locationId();
        }
        if(Context::eventId()) {
            // we are on an event page, so just show photos
            // for this event
            $filter['event_id'] = Context::eventId();
        }
        if(Context::isHome()) {
            $with_restricted = false;
        } else {
            $with_restricted = true;
        }
        
        $items = $this->Entry->getForDisplay(
            $filter,
            5, 
            $with_restricted
        );
        if($items) {
            usort($items, 'sort_items');
        }
    
        $this->set('data', $items);    
    }
    
    public function videos() {
        $this->loadModel('Entry');
    
        $filter = array(
            'filter' => array('video')
        );
        $identity_id = $this->getIdentityId();
        if($this->getIdentityId()) {
            $filter['identity_id'] = $identity_id;
        }
        
        if(Context::isHome()) {
            $with_restricted = false;
        } else {
            $with_restricted = true;
        }
        
        $items = $this->Entry->getForDisplay(
            $filter,
            5, 
            $with_restricted
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
    }
    
    public function form_account_edit() {
        $this->loadModel('Account');
        $this->retrieveFormErrors('Account');
        $params = Context::read('params.named');
        $account_id = $params['id'];
        $this->data = $this->Account->find(
            'first',
            array(
                'contain' => false,
                'conditions' => array(
                    'Account.id' => $account_id,
                    'Account.identity_id' => Context::loggedInIdentityId()
                )
            )
        );
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
    
    public function form_user_management() {
       $this->loadModel('Identity'); 
       $this->retrieveFormErrors('Identity');
        $this->set('data', $this->Identity->find('list', array('fields' => array('Identity.username'))));
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