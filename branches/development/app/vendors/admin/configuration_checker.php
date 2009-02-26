<?php

class ConfigurationChecker {
	protected $obsoleteConfigKeys = array('Language.default');
	protected $obsoleteConstants = array('NOSERUB_ADMIN_HASH',
										 'NOSERUB_ALLOW_TWITTER_BRIDGE', 
										 'NOSERUB_API_INFO_ACTIVE',
										 'NOSERUB_APP_NAME',
										 'NOSERUB_CDN_S3_ACCESS_KEY',
										 'NOSERUB_CDN_S3_SECRET_KEY',
										 'NOSERUB_CDN_S3_BUCKET',
										 'NOSERUB_CRON_HASH',
										 'NOSERUB_DOMAIN',
										 'NOSERUB_EMAIL_FROM',
										 'NOSERUB_FULL_BASE_URL',
										 'NOSERUB_GOOGLE_MAPS_KEY',
										 'NOSERUB_MANUAL_FEEDS_UPDATE',
										 'NOSERUB_REGISTRATION_RESTRICTED_HOSTS',
										 'NOSERUB_REGISTRATION_TYPE',
										 'NOSERUB_USE_CDN',
										 'NOSERUB_USE_FEED_CACHE',
										 'NOSERUB_USE_SSL',
										 'NOSERUB_XMPP_FULL_FEED_USER',
										 'NOSERUB_XMPP_FULL_FEED_PASSWORD', 
										 'NOSERUB_XMPP_FULL_FEED_SERVER',
										 'NOSERUB_XMPP_FULL_FEED_PORT');
	protected $configDefinitions = array();
    
	public function __construct() {
		$this->configDefinitions = array(
			new ConfigDefinition('NoseRub.admin_hash'),
			new ConfigDefinition('NoseRub.allow_twitter_bridge', new BooleanValidator()),
			#new ConfigDefinition('NoseRub.api_info_active', new BooleanValidator()),
			new ConfigDefinition('NoseRub.cdn_s3_access_key'),
			new ConfigDefinition('NoseRub.cdn_s3_secret_key'),
			new ConfigDefinition('NoseRub.cdn_s3_bucket'),
			new ConfigDefinition('NoseRub.cron_hash'),
			#new ConfigDefinition('NoseRub.default_language'),
			new ConfigDefinition('NoseRub.email_from'),
			#new ConfigDefinition('NoseRub.full_base_url', new FullBaseUrlValidator()),
			#new ConfigDefinition('NoseRub.google_maps_key', new FalseOrNonEmptyStringValidator()),
			new ConfigDefinition('NoseRub.manual_feeds_update', new BooleanValidator()),
			#new ConfigDefinition('NoseRub.registration_restricted_hosts', new FalseOrNonEmptyStringValidator()),
			#new ConfigDefinition('NoseRub.registration_type', new RegistrationTypeValidator()),
			new ConfigDefinition('NoseRub.use_cdn', new BooleanValidator()),
			#new ConfigDefinition('NoseRub.use_ssl', new BooleanValidator()),
			new ConfigDefinition('NoseRub.xmpp_full_feed_user'),
			new ConfigDefinition('NoseRub.xmpp_full_feed_password'),
			new ConfigDefinition('NoseRub.xmpp_full_feed_server'),
			new ConfigDefinition('NoseRub.xmpp_full_feed_port', new NumericValidator())
		);
	}

	public function check() {
		$out = $this->checkForObsoleteConstants();
		$out = am($out, $this->checkForObsoleteConfigKeys());
		$out = am($out, $this->checkForRequiredConfigSettings());
		
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