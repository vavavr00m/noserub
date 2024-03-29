<?php

/**
 * This is just a simple wrapper to make
 * the 'Context' more visible.
 */
class Context {
    public static function read($key = '') {
        if($key) {
            $key = 'context.' . $key;
        }
        return Configure::read($key);
    }
    
    public static function write($key, $value) {
        return Configure::write('context.' . $key, $value);
    }

    public static function isLoggedInIdentity() {
        return Configure::read('context.logged_in_identity');
    }
    
    public static function isSelf() {
        return Configure::read('context.is_self');
    }
    
    public static function isContact() {
        return Configure::read('context.is_contact');
    }
    
    /**
     * Wether the logged in user may send the profile
     * we look at send a message, or not.
     */
    public static function allowedSending() {
        return Configure::read('context.allowed_sending');
    }
    
    public static function isGuest() {
        return Configure::read('context.is_guest');
    }
    
    public static function isAdmin() {
        return Configure::read('context.admin_id') != 0;
    }
    
    public static function isLoggedIn() {
        return Context::loggedInIdentityId() > 0;
    }
    
    public static function isTwitterFeatureEnabled() {
    	return (Context::read('network.twitter_consumer_key') != '' && Context::read('network.twitter_consumer_secret') != '');
    }
    
    public static function unreadMessages() {
        if(Context::isSelf()) {
            return Configure::read('context.logged_in_identity.message_count');
        }
    }
    
    public static function messageId() {
        return Configure::read('context.message_id');
    }
    
    /**
     * wether the logged in user is subscribed to
     * the group from the URL
     */
    public static function isSubscribed() {
        return Configure::read('context.is_subscribed');
    }
    
    public static function loggedInIdentityId() {
        return Configure::read('context.logged_in_identity.id');
    }
    
    public static function networkId() {
        return Configure::read('context.network.id');
    }
    
    public static function networkName() {
        return Configure::read('context.network.name');
    }
    
    public static function setPage($page) {
        $split = explode('.', $page);
        $pageStructure = array();
        
        foreach($split as $part) {
            $pageStructure[] = strtolower($part);    
        }
        
        return Configure::write('context.page_structure', $pageStructure);
    }
    
    /**
     * @param string $pageStructure eg. profile.lifestream
     *
     * @return bool
     */
    public static function isPage($pageStructure) {
        $contextPageStructure = join('.', Configure::read('context.page_structure'));
        return strtolower($pageStructure) == $contextPageStructure;
    }
    
    public static function isProfile() {
        return Configure::read('context.page_structure.0') == 'profile';
    }
    
    public static function isHome() {
        return Configure::read('context.page_structure.0') == 'home';
    }
    
    /**
     * Returns the currently selected language
     * 
     * @param string $part - 'language' returns 'en' from 'en-us'
     *                       'country'  returns 'us' from 'en-us'
     *                       'all'      returns 'en-us' from 'en-us'
     *
     * @return string
     */
    public static function language($part = 'language') {
        $language = Configure::read('context.language');
        
        if($part == 'full') {
            return $language;
        }
        
        $parts = split('-', $language);
        if(count($parts) < 1) {
            return $language;
        }
        
        if($part == 'language') {
            return $parts[0];
        } else if($part == 'country') {
            return $parts[1];
        }
        
        return '';
    }
    
    public static function showMap() {
        if(Configure::read('context.location') && 
           round(Configure::read('context.location.latitude'), 0) != 0 &&
           round(Configure::read('context.location.longitude'), 0) != 0) {
            return true;
        }
        
        if(Configure::read('context.event') && 
           round(Configure::read('context.event.latitude'), 0) != 0 &&
           round(Configure::read('context.event.longitude'), 0) != 0) {
            return true;
        }
        
        if(Configure::read('context.identity') &&
           round(Configure::read('context.identity.latitude'), 0) != 0 &&
           round(Configure::read('context.identity.longitude'), 0) != 0) {
            return true;
        }
        
        return false;
    }
    
    public static function entryAddModus($modus = null) {
        if(is_null($modus)) {
            $modus = Configure::read('context.entry_add_modus');
            if(!$modus) {
                $modus = 'micropublish';
            }
        } else {
            Configure::write('context.entry_add_modus', $modus);
        }
        
        return $modus;
    } 
    
    public static function entryGroupAddModus($modus = null) {
        if(is_null($modus)) {
            $modus = Configure::read('context.entry_group_add_modus');
            if(!$modus) {
                $modus = 'text';
            }
        } else {
            Configure::write('context.entry_group_add_modus', $modus);
        }
        
        return $modus;
    }
    
    public static function contactFilter($filter = null) {
        if(is_null($filter)) {
            $filter = Configure::read('context.contact_filter');
        } else {
            Configure::write('context.contact_filter', $filter);
        }
    
        if(!is_array($filter)) {
            $filter = array();
        }
        
        return $filter;
    }
    
    public static function entryFilter($filter = null) {
        if(is_null($filter)) {
            $filter = Configure::read('context.entry_filter');
        } else {
            Configure::write('context.entry_filter', $filter);
        }
    
        if(!is_array($filter)) {
            $filter = array();
        }
        
        return $filter;
    }
    
    public static function entryId() {
        $entry_id = Configure::read('context.entry.id');
        
        return $entry_id ? $entry_id : 0;
    }
    
    public static function groupId() {
        $group_id = Configure::read('context.group.id');
        
        return $group_id ? $group_id : 0;
    }
    
    public static function groupSlug() {
        return Configure::read('context.group.slug');
    }
    
    public static function groupName() {
        return Configure::read('context.group.name');
    }
    
    public static function groupLastActivity() {
        return Configure::read('context.group.last_activity');
    }
    
    public static function locationId() {
        $location_id = Configure::read('context.location.id');
        
        return $location_id ? $location_id : 0;
    }
    
    public static function locationSlug() {
        return Configure::read('context.location.slug');
    }
    
    public static function locationName() {
        return Configure::read('context.location.name');
    }
    
    public static function locationLastActivity() {
        return Configure::read('context.location.last_activity');
    }
    
    public static function eventId() {
        $event_id = Configure::read('context.event.id');
        
        return $event_id ? $event_id : 0;
    }
    
    public static function eventSlug() {
        return Configure::read('context.event.slug');
    }
    
    public static function eventName() {
        return Configure::read('context.event.name');
    }
    
    public static function googleMapsKey() {
        return Configure::read('context.network.google_maps_key');
    }
    
     /*
     * Returns a copy of the whole context for usage in JS.
     * Because of this, some fields like the users hashed password
     * will be removed from the context.
     *
     * @return array
     */
    public static function forJs() {
        $data = Configure::read('context');
        
        unset($data['logged_in_identity']['password']);
        unset($data['logged_in_identity']['password_recovery_hash']);
        unset($data['logged_in_identity']['security_token']);
        unset($data['logged_in_identity']['hash']);
        
        unset($data['identity']['password']);
        unset($data['identity']['password_recovery_hash']);
        unset($data['identity']['security_token']);
        unset($data['identity']['hash']);
        unset($data['identity']['email']);
        unset($data['identity']['notify_comment']);
        unset($data['identity']['notify_contact']);
        unset($data['identity']['notify_favorite']);
        unset($data['identity']['overview_filters']);
        unset($data['identity']['address']);
        
        $data['base_url'] = Router::url('/', true);
        
        return $data;
    }
}

/**
 * a "context" array that will hold information about
 * the current status. That means: which pages is being
 * displayed, which is the logged in user, etc..
 * The goal is to have this universally available in all
 * controllers and all views.
 */
Configure::write('context', array(
    'logged_in_identity' => false,
    'network' => array('id' => 1), // default for now, needed for old menu component
    'identity' => false, // the identity we're looking at,
    'is_self' => false, // wether the identity we look at is the logged in identity
    'is_guest' => false, // wether the identity only logged in with OpenID, without account
    'admin_id' => false, // wether the identity is logged in with admin access right now,
    'page_structure' => array(), // on which page/subpage are we?
));