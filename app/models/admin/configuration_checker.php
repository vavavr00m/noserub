<?php

class ConfigurationChecker {
	protected $obsoleteConfigKeys = array();
	protected $obsoleteConstants = array('NOSERUB_DOMAIN', 'NOSERUB_USE_FEED_CACHE');
	protected $requiredConfigKeys = array();
	
    public $constants = array('NOSERUB_ADMIN_HASH' => array(
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
                           'NOSERUB_MANUAL_FEEDS_UPDATE' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false)),
                           'NOSERUB_USE_CDN' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false))
                          );
    
	public function check() {
		$out = $this->checkForObsoleteConstants();
		$out = am($out, $this->checkForObsoleteConfigKeys());
		$out = am($out, $this->checkForRequiredConfigKeys());
		$out = am($out, $this->checkConstants());
		
		return $out;
	}

	protected function checkForObsoleteConfigKeys() {
		$out = array();
		
		foreach ($this->obsoleteConfigKeys as $obsoleteConfigKey) {
			if ($this->isConfigKeySet($obsoleteConfigKey)) {
				$out[$obsoleteConfigKey] = __('obsolete! Please remove it from noserub.php', true);
			}
		}
		
		return $out;
	}
	
	protected function checkForObsoleteConstants() {		
		$out = array();
		
		foreach ($this->obsoleteConstants as $obsoleteConstant) {
			if (defined($obsoleteConstant)) {
				$out[$obsoleteConstant] = __('obsolete! Please remove it from noserub.php', true);
			}
		}
		
		return $out;
	}
	
	protected function checkForRequiredConfigKeys() {
		$out = array();
		
		foreach ($this->requiredConfigKeys as $requiredConfigKey) {
			if (!$this->isConfigKeySet($requiredConfigKey)) {
				$out[$requiredConfigKey] = __('not defined in noserub.php', true);
			}
		}
		
		return $out;
	}
	
	private function isConfigKeySet($key) {
		// XXX there is currently no way to determine whether a config key is 
		// set, so we assume it is set if Configure::read() doesn't return null.
		// see also https://trac.cakephp.org/ticket/5743
		return !is_null(Configure::read($key));
	}
	
    public function checkConstants() {
        $out = array();
        foreach($this->constants as $constant => $info) {
            if(!defined($constant)) {
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
        
        if (!isset($out['NOSERUB_FULL_BASE_URL'])) {
        	if (!$this->verifyFullBaseUrlEndsWithSlash()) {
        		$out['NOSERUB_FULL_BASE_URL'] = sprintf(__('value must end with a slash! (see %s)', true), $info['file']);
        	}
        }
        
        return $out;
    }
    
    private function verifyFullBaseUrlEndsWithSlash() {
    	return (strpos(strrev(constant('NOSERUB_FULL_BASE_URL')), '/') === 0);
    }
}