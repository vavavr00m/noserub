<?php

class ConfigurationChecker {
	protected $obsoleteConfigKeys = array();
	protected $obsoleteConstants = array('NOSERUB_ALLOW_TWITTER_BRIDGE', 
										 'NOSERUB_API_INFO_ACTIVE',
										 'NOSERUB_CDN_S3_ACCESS_KEY',
										 'NOSERUB_CDN_S3_SECRET_KEY',
										 'NOSERUB_CDN_S3_BUCKET',
										 'NOSERUB_DOMAIN',
										 'NOSERUB_GOOGLE_MAPS_KEY',
										 'NOSERUB_REGISTRATION_RESTRICTED_HOSTS',
										 'NOSERUB_USE_CDN',
										 'NOSERUB_USE_FEED_CACHE', 
										 'NOSERUB_XMPP_FULL_FEED_USER',
										 'NOSERUB_XMPP_FULL_FEED_PASSWORD', 
										 'NOSERUB_XMPP_FULL_FEED_SERVER',
										 'NOSERUB_XMPP_FULL_FEED_PORT');
	protected $configDefinitions = array();
	
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
                           'NOSERUB_APP_NAME' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_FULL_BASE_URL' => array(
                               'file' => 'noserub.php'),
                           'NOSERUB_MANUAL_FEEDS_UPDATE' => array(
                               'file'   => 'noserub.php',
                               'values' => array(true, false))
                          );
    
	public function __construct() {
		$this->configDefinitions = array(
			new ConfigDefinition('Noserub.allow_twitter_bridge', new BooleanValidator()),
			new ConfigDefinition('Noserub.api_info_active', new BooleanValidator()),
			new ConfigDefinition('Noserub.cdn_s3_access_key'),
			new ConfigDefinition('Noserub.cdn_s3_secret_key'),
			new ConfigDefinition('Noserub.cdn_s3_bucket'),
			new ConfigDefinition('Noserub.google_maps_key', new FalseOrNonEmptyStringValidator()),
			new ConfigDefinition('Noserub.registration_restricted_hosts', new FalseOrNonEmptyStringValidator()),
			new ConfigDefinition('Noserub.use_cdn', new BooleanValidator()),
			new ConfigDefinition('Noserub.xmpp_full_feed_user'),
			new ConfigDefinition('Noserub.xmpp_full_feed_password'),
			new ConfigDefinition('Noserub.xmpp_full_feed_server'),
			new ConfigDefinition('Noserub.xmpp_full_feed_port', new NumericValidator())
		);
	}

	public function check() {
		$out = $this->checkForObsoleteConstants();
		$out = am($out, $this->checkForObsoleteConfigKeys());
		$out = am($out, $this->checkForRequiredConfigSettings());
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
	
	protected function checkForRequiredConfigSettings() {
		$out = array();

		foreach ($this->configDefinitions as $configDefinition) {
			if ($this->isConfigKeySet($configDefinition->getKey())) {
				$result = $this->validateConfigValue($configDefinition);
				
				if ($result !== true) {
					$out[$configDefinition->getKey()] = $result;
				}
			} else {
				$out[$configDefinition->getKey()] = __('not defined in noserub.php', true);
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
	
	private function validateConfigValue(ConfigDefinition $definition) {
		if ($definition->hasValidator()) {
			$validator = $definition->getValidator();
			
			return $validator->validate(Configure::read($definition->getKey()));
		}
		
		return true;
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

class ConfigDefinition {
	private $key = null;
	private $validator = null;
	
	public function __construct($key, ConfigValueValidator $validator = null) {
		$this->key = $key;
		$this->validator = $validator;
	}
	
	public function getKey() {
		return $this->key;
	}
	
	public function hasValidator() {
		return !is_null($this->validator);
	}
	
	public function getValidator() {
		return $this->validator;
	}
}

interface ConfigValueValidator {
	/**
	 * Returns true if the value validates, otherwise a message with the 
	 * reason why the validation failed
	 */
	public function validate($value);
}

class BooleanValidator implements ConfigValueValidator {
	public function validate($value) {
		if (is_bool($value)) {
			return true;
		}
		
		return __('value might only be: true or false', true);
	}
}

class FalseOrNonEmptyStringValidator implements ConfigValueValidator {
	public function validate($value) {
		if ($value === false || $this->isNonEmptyString($value)) {
			return true;
		}
		
		return __('value must be false or a string!', true);
	}
	
	private function isNonEmptyString($value) {
		return (is_string($value) && $value != '');
	}
}

class FullBaseUrlValidator implements ConfigValueValidator {
	public function validate($value) {
		if ($this->endsWithSlash($value)) {
			return true;
		}
		
		return __('value must end with a slash!', true);
	}
	
	private function endsWithSlash($string) {
		return (strpos(strrev($string), '/') === 0);
	}
}

class NumericValidator implements ConfigValueValidator {
	public function validate($value) {
		if (is_numeric($value)) {
			return true;
		}
		
		return __('value must be numeric!', true);
	}
}

class RegistrationTypeValidator implements ConfigValueValidator {
	private $validRegistrationTypes = array('all', 'none', 'invitation');
	
	public function validate($value) {
		if (in_array($value, $this->validRegistrationTypes)) {
			return true;
		}
		
		return sprintf(__('value might only be: "%s"', true), join('", "', $this->validRegistrationTypes));
	}
}