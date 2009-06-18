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
    
    public static function isGuest() {
        return Configure::read('context.is_guest');
    }
    
    public static function isAdmin() {
        return Configure::read('context.admin_id') != 0;
    }
    
    public static function loggedInIdentityId() {
        return Configure::read('context.logged_in_identity.id');
    }
    
    public static function networkId() {
        return Configure::read('context.network.id');
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
    'page_structure' => array() // on which page/subpage are we?
));