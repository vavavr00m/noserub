<?php

App::import('Vendor', 'oauth-consumer'.DS.'oauth_consumer');
App::import('Vendor', 'constants'.DS.'twitter');

class TwitterAccountsController extends AppController {
	public $uses = array('TwitterAccount');
	public $components = array('RequestHandler');

	public function index() {
		if ($this->RequestHandler->isGet()) {
			if (Context::isTwitterFeatureEnabled()) {
				if (isset($this->params['url']['oauth_token'])) {
					$requestToken = $this->Session->read('twitter_request_token');
					$consumer = $this->createConsumer();
					$accessToken = $consumer->getAccessToken(Twitter::ACCESS_TOKEN_URL, $requestToken);
					$this->TwitterAccount->saveAccessToken(Context::loggedInIdentityId(), $accessToken->key, $accessToken->secret);
				}
				$this->set('hasTwitterAccount', $this->TwitterAccount->hasAny(array('identity_id' => Context::loggedInIdentityId())));
			} else {
				$this->render('twitter_feature_disabled');
			}
		} else {
			$consumer = $this->createConsumer();
			$requestToken = $consumer->getRequestToken(Twitter::REQUEST_TOKEN_URL);
			$this->Session->write('twitter_request_token', $requestToken);
			$this->redirect(Twitter::AUTHORIZE_URL . '?oauth_token=' . $requestToken->key . '&oauth_callback=' . urlencode(FULL_BASE_URL . $this->here));
		}
	}

	public function delete() {
		$this->ensureSecurityToken();

		if ($this->TwitterAccount->deleteByIdentityId(Context::loggedInIdentityId())) {
			$this->flashMessage('success', __('Twitter Account successfully removed', true));
		} else {
			$this->flashMessage('alert', __('Twitter Account couldn\'t be removed', true));
		}

		$this->redirect('/settings/twitter');
	}

	private function createConsumer() {
		return new OAuth_Consumer(Context::read('network.twitter_consumer_key'), Context::read('network.twitter_consumer_secret'));
	}
}
