<?php

class WebExtractor {
	/**
	 * This function fetches urls, trying curl and file_get_contents
	 * 
	 *  using code by lars.strojny - http://code.google.com/p/noserub/issues/detail?id=167
	 */
	public function fetchUrl($url){
		if (!ini_get('allow_url_fopen')) {
			if (!function_exists('curl_init')) {
				throw new RuntimeException('allow_url_fopen disabled and curl not available - No possibility to fetch external resources.');
			} else {
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_USERAGENT, NOSERUB_USER_AGENT);
				$content = curl_exec($curl);
				curl_close($curl);
				return $content;
			}
		} else {
			return @file_get_contents($url);
		}
	}
}