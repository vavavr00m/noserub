<?php

class TwitterSettingsController extends AppController {
	public $components = array('RequestHandler');

	public function index() {
		if ($this->RequestHandler->isGet()) {
			$this->set('isTwitterFeatureEnabled', $this->isTwitterFeatureEnabled());
		}
		// TODO implement this method
	}

	private function isTwitterFeatureEnabled() {
		return (Configure::read('context.network.twitter_consumer_key') != '' &&
				Configure::read('context.network.twitter_consumer_secret') != '');
	}
}
