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
                    $out[$constant] = 'obsolete! Please remove it from '.$info['file'];
                }
            } else if(!defined($constant)) {
                $out[$constant] = 'not defined! (see '.$info['file'].')';
            } else {
                if(isset($info['values'])) {
                    if(!in_array(constant($constant), $info['values'])) {
                        $out[$constant] = 'value might only be: "' . join('", "', $info['values']) . '" (see '.$info['file'].')';
                    }
                } else {
                    if(constant($constant) === '') {
                        $out[$constant] = 'no value! (see '.$info['file'].')';
                    }
                }
            }
        }
        
        return $out;
    }
    
    public function checkExtensions() {
        $result = array();
    	
        if (!extension_loaded('curl')) {
        	$result = am($result, array('curl' => 'needed for communicating with other servers'));
        }
        
    	if (!extension_loaded('gd')) {
        	$result = am($result, array('GD' => 'needed for image handling')); 
        }
    	
        if (!(function_exists('gmp_init') || function_exists('bcscale'))) {
        	$result = am($result, array('GMP or BCMath' => 'needed for OpenID functionality'));
        }
        
        return $result;
    }
}