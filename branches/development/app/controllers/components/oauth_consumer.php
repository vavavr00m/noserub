<?php
/**
 * A simple OAuth consumer component for CakePHP.
 * 
 * Requires the OAuth library from http://oauth.googlecode.com/svn/code/php/
 * 
 * Copyright (c) by Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Core', 'http_socket');

class OauthConsumerComponent extends Object {
	
	/**
	 * Call API with a GET request
	 */
	public function get($consumerName, $url, $key, $secret) {
		$accessToken = new OAuthToken($key, $secret);
		$request = $this->prepareRequest($consumerName, $accessToken, 'GET', $url, array());
		
		return $this->doGet($request->to_url());
	}
	
	public function getAccessToken($consumerName, $accessTokenURL, $requestToken, $httpMethod = 'POST', $parameters = array()) {
		$request = $this->prepareRequest($consumerName, $requestToken, $httpMethod, $accessTokenURL, $parameters);
		
		if ($httpMethod == 'POST') {
			$data = $this->doPost($accessTokenURL, $request->to_postdata());
		} else {
			$data = $this->doGet($request->to_url());
		}

		parse_str($data);
		
		return new OAuthToken($oauth_token, $oauth_token_secret);
	}
	
	public function getRequestToken($consumerName, $requestTokenURL, $httpMethod = 'POST', $parameters = array()) {
		$request = $this->prepareRequest($consumerName, null, $httpMethod, $requestTokenURL, $parameters);
		
		if ($httpMethod == 'POST') {
			$data = $this->doPost($requestTokenURL, $request->to_postdata());
		} else {
			$data = $this->doGet($request->to_url());
		}
		
		parse_str($data);

		return new OAuthToken($oauth_token, $oauth_token_secret);
	}
	
	/**
	 * Call API with a POST request
	 */
	public function post($consumerName, $url, $postData, $key, $secret) {
		$accessToken = new OAuthToken($key, $secret);
		$request = $this->prepareRequest($consumerName, $accessToken, 'POST', $url, $postData);
		
		return $this->doPost($url, $request->to_postdata());
	}
	
	private function createConsumer($consumerName) {
		$CONSUMERS_PATH = COMPONENTS.'oauth_consumers'.DS;
		App::import('File', 'abstractConsumer', array('file' => $CONSUMERS_PATH.'abstract_consumer.php'));
		
		$fileName = Inflector::underscore($consumerName) . '_consumer.php';
		$className = $consumerName . 'Consumer';
		
		if (App::import('File', $fileName, array('file' => $CONSUMERS_PATH.$fileName))) {
			$consumerClass = new $className();
			return $consumerClass->getConsumer();
		} else {
			throw new InvalidArgumentException('Consumer ' . $fileName . ' not found!');
		}
	}
	
	private function doGet($url) {
		$socket = new HttpSocket();
		return $socket->get($url);
	}
	
	private function doPost($url, $data) {
		$socket = new HttpSocket();
		return $socket->post($url, $data);
	}
	
	private function prepareRequest($consumerName, $token, $httpMethod, $url, $parameters) {
		$consumer = $this->createConsumer($consumerName);
		$request = OAuthRequest::from_consumer_and_token($consumer, $token, $httpMethod, $url, $parameters);
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
		
		return $request;
	}
}