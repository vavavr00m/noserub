<?php

class WidgetsController extends AppController {
    
    public $components = array('cluster');
    
    /**
     * Various elements / pages
     */
     public function navigation() {
         $type = isset($this->params['type']) ? $this->params['type'] : 'main';
         $this->render($type . '_navigation');
 	}
 	
 	/**
 	 * Filters
 	 */
 	 
 	public function contact_filter() {
 	    if($this->context['is_self']) {
 	        $this->dynamicUse('Contact');
 	    
 	        $this->set('contact_tags', $this->Contact->getTagList($this->context['logged_in_identity']['id']));
        } else {
            return false;
        }
 	}
 	
    /**
     * Identities / Users
     */
    public function new_users() {
        $this->dynamicUse('Identity');
        
	    $this->set('data', $this->Identity->getNewbies($this->context, 9));
	}
	
	/**
	 * Contacts
	 */

    /**
     * contacts of a given identity.
     */
    public function contacts_for_identity() {
        $this->dynamicUse('Contact');
         
        $identity_id = $this->getIdentityId();
        $this->Contact->Identity->contain();
        $this->Contact->Identity->id = $identity_id;
        $this->set('identity', $this->Contact->Identity->read());
        
        $tag_filter = $this->Session->read('Filter.Contact.Tag');
        if(!$tag_filter) {
            $tag_filter = 'all';
        }
        
        # get (filtered) contacts
        if($this->context['is_self']) {
            # this is my network, so I can show every contact
            $contact_filter = array('tag' => $tag_filter);
        } else {
            # this is someone elses network, so I show only the noserub contacts
            $contact_filter = array('tag' => $tag_filter, 'type' => 'public');
        }
        $data = $this->Contact->getForDisplay($identity_id, $contact_filter, 9);
        $contacts = array();
        foreach($data as $key => $value) {
            $contacts[] = $value['WithIdentity'];
        }
        $this->set('data', $contacts);
     }

     public function my_contacts() {
        $this->dynamicUse('Contact');
        
        $logged_in_identity_id = $this->Session->read('Identity.id');
        if(!$logged_in_identity_id) {
            return false;
        }
        
        # get contacts of the displayed profile
        $all_contacts = $this->Contact->getForIdentity($logged_in_identity_id);
        
        $contacts = array();
        $num_private_contacts = 0;
        $num_noserub_contacts = 0;
        foreach($all_contacts as $contact) {
            if(strpos($contact['WithIdentity']['username'], '@') === false) {
                $num_noserub_contacts++;
                if(count($contacts) < 9) {
                    $contacts[] = $contact;
                }
            } else {
                $num_private_contacts++;
                if(count($contacts) < 9) {
                    $contacts[] = $contact;
                }
            }
        }
        $this->set('num_private_contacts', $num_private_contacts);
        $this->set('num_noserub_contacts', $num_noserub_contacts);
        $this->set('data', $contacts);
     }
     
    /**
     * Lifestream
     **/
     
    public function lifestream() {
        $this->dynamicUse('Contact');
        
        $type = $this->params['type'];

        $show_in_overview = $this->Contact->Identity->Account->ServiceType->getDefaultFilters();
        $filter = $show_in_overview;

        if($type == 'network') {
            $data = $this->Contact->getForDisplay($this->context['logged_in_identity']['id'], array('tag' => 'all'));
        
            # we need to go through all this now and get Accounts and Services
            # also save all contacts
            $contacts = array();
            foreach($data as $key => $value) {
                $contacts[] = $value['WithIdentity'];
            }

            $contact_ids = Set::extract($contacts, '{n}.id');
            $contact_ids[] = $this->context['logged_in_identity']['id'];
        } else {
            $contact_ids = array($this->context['identity']['id']);
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
    
    /**
     * private methods
     */
      
    /**
     * imports model when they are not available yet. this
     * is similar to the uses() array in Cake, but more specific
     * to what is needed.
     *
     * @param mixed $models either string or array of model names
     */
     private function dynamicUse($models) {
         if(!is_array($models)) {
             $models = array($models);
         }

        foreach($models as $model) {
             if(!isset($this->{$model})) {
                 App::import('Model', $model);
                 eval ("\$this->{$model} = new {$model}();");
             }
         }
     }
     
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
            if($this->context['identity']) {
                $identity_id = $this->context['identity']['id'];
            } else if($this->context['logged_in_identity']) {
                # if not, use the logged in identity
                $identity_id = $this->context['logged_in_identity']['id'];
            }
         }
     
        return $identity_id;
     }
}