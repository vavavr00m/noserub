<?php

class WidgetsController extends AppController {
    
    /**
     * Various elements / pages
     */
     public function navigation() {
         $type = isset($this->params['type']) ? $this->params['type'] : 'main';

 	    if($this->context['logged_in_identity']) {
 	        $this->dynamicUse('Contact');

 	        $this->set('contact_tags', $this->Contact->getTagList($this->context['logged_in_identity']['id']));     	    
         }
         
         $this->render($type . '_navigation');
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
         
         $session_identity = $this->Session->read('Identity');

         $identity_id = $this->params['identity_id'];
         $this->Contact->Identity->contain();
         $this->Contact->Identity->id = $identity_id;
         $this->set('identity', $this->Contact->Identity->read());

         $tag_filter = $this->Session->read('Filter.Contact.Tag');
         if(!$tag_filter) {
             $tag_filter = 'all';
         }

         $is_self = $session_identity && $session_identity['id'] == $identity_id;
         # get (filtered) contacts
         if($is_self) {
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
}