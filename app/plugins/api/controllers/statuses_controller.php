<?php
/* Part of the twitter-compatible API */
class StatusesController extends ApiAppController {
	public $components = array('OauthServiceProvider');
	const DEFAULT_LIMIT = 20;
	
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
	
	public function mentions() {
		// TODO implement
	}
	
	public function public_timeline() {
		$this->loadModel('Entry');
		$this->set('data', array('statuses' => $this->formatStatuses($this->Entry->getForDisplay(array(), self::DEFAULT_LIMIT, false))));
	}
	
	public function show() {
		if (!isset($this->params['pass'][0])) {
			$this->respondWithNoStatusFound();
	        return;
		}
		
		$entry_id = $this->params['pass'][0];
		
		$this->loadModel('Entry');
		$status = $this->Entry->getForDisplay(array('entry_id' => $entry_id), 1);

		if ($status) {
			$this->set('data', $this->formatStatuses($status));
		} else {
			$this->respondWithNoStatusFound();
		}
	}
	
	public function update() {
		// TODO implement
	}
	
	public function user_timeline() {
		if (isset($this->params['pass'][0])) {
			if (is_numeric($this->params['pass'][0])) {
				$identity_id = $this->params['pass'][0];
			}
		}
		
		$this->loadModel('Entry');
		$conditions = array('identity_id' => $identity_id);
		$this->set('data', array('statuses' => $this->formatStatuses($this->Entry->getForDisplay($conditions, self::DEFAULT_LIMIT, false))));
	}
	
	private function formatStatuses(array $statuses) {
		App::import('Vendor', 'Api.ArrayFactory');
		$data = array();
		
		foreach ($statuses as $status) {
			$data[] = ArrayFactory::status_with_user($status);
		}
		
		return $data;
	}
	
	private function respondWithNoStatusFound() {
		header("HTTP/1.1 404 Not Found");
	    $this->set('data', array('hash' => array('request' => $this->params['url']['url'], 
	        									 'error' => 'No status found with that ID.')));
		
	}
}