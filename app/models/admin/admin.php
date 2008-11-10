<?php

/** 
 * Model for all the admin stuff in NoseRub.
 */
class Admin extends AppModel {
    public $useTable = false;

    public $constants = array('!NOSERUB_DOMAIN' => array(
                                'file' => 'noserub.php'),
                           'NOSERUB_ADMIN_HASH' => array(
                                'file' => 'noserub.php'),
                           'NOSERUB_REGISTRATION_TYPE' => array(
                                'values' => array('all', 'none', 'invitation'),
                                'file'   => 'noserub.php'),
                           'NOSERUB_EMAIL_FROM' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_USE_SSL' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false)),
                           'NOSERUB_GOOGLE_MAPS_KEY' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_APP_NAME' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_FULL_BASE_URL' => array(
                               'file' => 'noserub.php'),
                           '!NOSERUB_USE_FEED_CACHE' => array(
                               'file'       => 'noserub.php'),
                           'NOSERUB_MANUAL_FEEDS_UPDATE' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false)),
                           'NOSERUB_USE_CDN' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false))
                          );
    
    /**
     * checks if some directories are writeable
     */
    public function checkWriteable() {
    	$writeableDirectories = array(APP.'tmp', WWW_ROOT.'static'.DS.'avatars');
    	
        $out = array();
        foreach($writeableDirectories as $directory) {
            if(!is_writeable($directory)) {
                $out[] = $directory;
            }
        }
        
        return $out;
    }
    
    /**
     * check some constants
     *
     * @param  
     * @return 
     * @access 
     */
    public function checkConstants() {
        $out = array();
        foreach($this->constants as $constant => $info) {
            if(strpos($constant, '!') === 0) {
                $constant = str_replace('!', '', $constant);
                if(defined($constant)) {
                    $out[$constant] = sprintf(__('obsolete! Please remove it from %s', true), $info['file']);
                }
            } else if(!defined($constant)) {
                $out[$constant] = sprintf(__('not defined! (see %s)', true), $info['file']);
            } else {
                if(isset($info['values'])) {
                    if(!in_array(constant($constant), $info['values'])) {
                        $out[$constant] = sprintf(__('value might only be: "%s" (see %s)', true), join('", "', $info['values']), $info['file']);
                    }
                } else {
                    if(constant($constant) === '') {
                        $out[$constant] = sprintf(__('no value! (see %s)', true), $info['file']);
                    }
                }
            }
        }
        
        return $out;
    }
    
    public function checkExtensions() {
        $result = array();
    	
        if (!extension_loaded('curl')) {
        	$result = am($result, array('curl' => __('needed for communicating with other servers', true)));
        }
        
    	if (!extension_loaded('gd')) {
        	$result = am($result, array('GD' => __('needed for image handling', true))); 
        }
    	
        if (!(function_exists('gmp_init') || function_exists('bcscale'))) {
        	$result = am($result, array(__('GMP or BCMath', true) => __('needed for OpenID functionality', true)));
        }
        
        return $result;
    }
}