<?php

App::import('Vendor', 'cryptutil', array('file' => 'Auth'.DS.'OpenID'.DS.'CryptUtil.php'));

class RandomNumberGeneratorChecker {
	public static function check() {
		if (self::usePseudoRandomNumberGenerator()) {
			return true;
		}

		return self::isRandomNumberGeneratorReadable();
	}

	private static function isRandomNumberGeneratorReadable() {
		$f = @fopen(Auth_OpenID_RAND_SOURCE, "r");
		
		if ($f === false) {
			return __('No random number generator found. Please uncomment Auth_OpenID_RAND_SOURCE in app/config/noserub.php to continue with an insecure random number generator', true);
		}

		return true;
	}

	private static function usePseudoRandomNumberGenerator() {
		return (Auth_OpenID_RAND_SOURCE === null);
	}
}
