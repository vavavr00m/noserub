<?php

App::import('Vendor', 'oauth-consumer'.DS.'oauth_consumer');

class TwitterAccountsController extends AppController {
	public $uses = array('TwitterAccount');
	public $components = array('RequestHandler');
	const ACCESS_TOKEN_URL = 'http://twitter.com/oauth/access_token';
	const REQUEST_TOKEN_URL = 'http://twitter.com/oauth/request_token';
	const AUTHORIZE_URL = 'http://twitter.com/oauth/authorize';
	private $consumerKey = '';
	private $consumerSecret = '';
	private $identityID = '';

	public function beforeFilter() {
		parent::beforeFilter();
		$this->consumerKey = Context::read('network.twitter_consumer_key');
		$this->consumerSecret = Context::read('network.twitter_consumer_secret');
		$this->identityID = Context::read('logged_in_identity.id');
	}

	public function index() {
		if ($this->RequestHandler->isGet()) {
			if (isset($this->params['url']['oauth_token'])) {
				$requestToken = $this->Session->read('twitter_request_token');
				$consumer = $this->createConsumer();
				$accessToken = $consumer->getAccessToken(self::ACCESS_TOKEN_URL, $requestToken);
				$this->TwitterAccount->saveAccessToken($this->identityID, $accessToken->key, $accessToken->secret);
			}
			$this->set('isTwitterFeatureEnabled', $this->isTwitterFeatureEnabled());
			$this->set('hasTwitterAccount', $this->TwitterAccount->hasAny(array('identity_id' => $this->identityID)));
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
