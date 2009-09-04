<?php
/* Part of the twitter-compatible API */
class StatusesController extends ApiAppController {
	public $components = array('OauthServiceProvider', 'Responder');
	const DEFAULT_LIMIT = 20;
	const MAX_LIMIT = 200;
	
	public function destroy() {
		// TODO implement
	}
	
	public function followers() {
		// TODO implement
	}
	
	public function friends() {
		// TODO implement
	}
	
	public function friends_timeline() {
		$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
		$accessToken = ClassRegistry::init('AccessToken');
		$identity_id = $accessToken->field('identity_id', array('token_key' => $key));
		
		$this->loadModel('Contact');

        $data = $this->Contact->getForDisplay($identity_id, array());
		$contacts = array();
        foreach($data as $key => $value) {
        	$contacts[] = $value['WithIdentity'];
        }
        $contact_ids = Set::extract($contacts, '{n}.id');
        $contact_ids[] = $identity_id;

        $conditions = array('identity_id' => $contact_ids);
        $this->set('data', array('statuses' => $this->formatStatuses($this->Contact->Identity->Entry->getForDisplay($conditions, self::DEFAULT_LIMIT, true))));		
	}
	
	public function home_timeline() {
		// TODO implement (upcoming feature of twitter)
	}
	
	public function mentions() {
		// TODO implement
	}
	
	public function public_timeline() {
		$this->loadModel('Entry');
		$this->set('data', array('statuses' => $this->formatStatuses($this->Entry->getForDisplay(array(), self::DEFAULT_LIMIT, false))));
	}
	
	public function retweet() {
		// TODO implement (upcoming feature of twitter)
	}
	
	public function retweeted_by_me() {
		// TODO implement (upcoming feature of twitter)
	}
	
	public function retweeted_to_me() {
		// TODO implement (upcoming feature of twitter)
	}
	
	public function retweets_of_me() {
		// TODO implement (upcoming feature of twitter)
	}
	
	public function show() {
		if (!isset($this->params['pass'][0])) {
			$this->Responder->respondWithNoStatusFound();
	        return;
		}
		
		$entry_id = $this->params['pass'][0];
		
		$this->loadModel('Entry');
		$status = $this->Entry->getForDisplay(array('entry_id' => $entry_id), 1);

		if ($status) {
			$this->set('data', $this->formatStatuses($status));
		} else {
			$this->Responder->respondWithNoStatusFound();
		}
	}
	
	public function update() {
		// TODO implement
	}
	
	public function user_timeline() {
		$user_id = $this->getUserIdParameter();
		$screen_name = $this->getScreenNameParameter();
		
		if (!$user_id && !$screen_name) {
			$this->Responder->respondWithUserNotFound();
	        return;
		}
		
		if ($screen_name) {
			$user_id = ClassRegistry::init('Identity')->username2IdentityId($this->buildUsername($screen_name));
		}
		
		if ($user_id) {
			$this->loadModel('Entry');
			$conditions = array('identity_id' => $user_id);
			$this->set('data', array('statuses' => $this->formatStatuses($this->Entry->getForDisplay($conditions, $this->getCount(), false))));
		} else {
			$this->Responder->respondWithUserNotFound();
		}
	}
	
	private function buildUsername($screen_name) {
		App::import('Vendor', 'UrlUtil');
		return UrlUtil::removeHttpAndHttps(Context::read('network.url')).$screen_name;
	}
	
	private function getCount() {
		if (isset($this->params['url']['count'])) {
			$count = $this->params['url']['count'];
			
			if (is_numeric($count)) {
				if ($count > self::MAX_LIMIT) {
					return self::MAX_LIMIT;
				} elseif ($count <= 0) {
					return self::DEFAULT_LIMIT;
				}
				
				return $count;
			}
		}
		
		return self::DEFAULT_LIMIT;
	}
	
	private function getScreenNameParameter() {
		if (isset($this->params['url']['screen_name'])) {
			return $this->params['url']['screen_name'];
		}
		
		if (isset($this->params['pass'][0]) && !is_numeric($this->params['pass'][0])) {
			return $this->params['pass'][0];
		}
		
		return false;
	}
	
	private function getUserIdParameter() {
		if (isset($this->params['url']['user_id'])) {
			return $this->params['url']['user_id'];
		}
		
		if (isset($this->params['pass'][0]) && is_numeric($this->params['pass'][0])) {
			return $this->params['pass'][0];
		}
		
		return false;
	}
	
	private function formatStatuses(array $statuses) {
		App::import('Vendor', 'Api.ArrayFactory');
		$data = array();
		
		foreach ($statuses as $status) {
			$data[] = ArrayFactory::status_with_user($status);
		}
		
		return $data;
	}
}