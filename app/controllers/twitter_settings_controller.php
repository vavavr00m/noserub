<?php

App::import('Vendor', 'oauth-consumer'.DS.'oauth_consumer');

class TwitterSettingsController extends AppController {
	public $components = array('RequestHandler');
	const ACCESS_TOKEN_URL = 'http://twitter.com/oauth/access_token';
	const REQUEST_TOKEN_URL = 'http://twitter.com/oauth/request_token';
	const AUTHORIZE_URL = 'http://twitter.com/oauth/authorize';
	private $consumerKey = '';
	private $consumerSecret = '';

	public function beforeFilter() {
		parent::beforeFilter();
		$this->consumerKey = Configure::read('context.network.twitter_consumer_key');
		$this->consumerSecret = Configure::read('context.network.twitter_consumer_secret');
	}

	public function index() {
		if ($this->RequestHandler->isGet()) {
			if (isset($this->params['url']['oauth_token'])) {
				$requestToken = $this->Session->read('twitter_request_token');
				$consumer = $this->createConsumer();
				$accessToken = $consumer->getAccessToken(self::ACCESS_TOKEN_URL, $requestToken);
				$twitterAccount = ClassRegistry::init('TwitterAccount');
				$twitterAccount->saveAccessToken(Configure::read('context.logged_in_identity.id'), $accessToken->key, $accessToken
				->secret);
			}
			$this->set('isTwitterFeatureEnabled', $this->isTwitterFeatureEnabled());
		} else {
			$consumer = $this->createConsumer();
			$requestToken = $consumer->getRequestToken(self::REQUEST_TOKEN_URL);
			$this->Session->write('twitter_request_token', $requestToken);
			$this->redirect(self::AUTHORIZE_URL . '?oauth_token=' . $requestToken->key . '&oauth_callback=' . urlencode(FULL_BASE_URL . $this->here));
		}
	}

	private function createConsumer() {
		return new OAuth_Consumer($this->consumerKey, $this->consumerSecret);
	}

	private function isTwitterFeatureEnabled() {
		return ($this->consumerKey != '' && $this->consumerSecret != '');
	}
}
