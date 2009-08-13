<?php
/* Part of the twitter-compatible API */
class UsersController extends ApiAppController {
	
	public function show() {
		$user_id = $this->getUserIdParameter();
		$screen_name = $this->getScreenNameParameter();
		
		if (!$user_id && !$screen_name) {
			$this->respondWithUserNotFound();
	        return;
		}
		
		if ($user_id) {
			// TODO find_by_user_id
		} else {
			// TODO find_by_screen_name
		}
		
		$this->set('data', array('user' => $this->formatData()));
	}
	
	private function formatData() {
		/*return array('id' => $status['Entry']['identity_id'],
					  'name' => $status['Identity']['firstname'] . ' ' . $status['Identity']['lastname'], // TODO
					  'screen_name' => $status['Identity']['single_username'],
				      'location' => null, // TODO
				      'description' => null, // TODO
				      'profile_image_url' => null, // TODO
				      'url' => null, // TODO
				      'protected' => 'false',
				      'followers_count' => null, // TODO
				      'profile_background_color' => null, // not supported by NoseRub
				      'profile_text_color' => null, // not supported by NoseRub
				      'profile_link_color' => null, // not supported by NoseRub
				      'profile_sidebar_fill_color' => null, // not supported by NoseRub
				      'profile_sidebar_border_color' => null, // not supported by NoseRub
				      'friends_count' => null, // TODO
				      'created_at' => null, // TODO
				      'favourites_count' => null, // TODO
				      'utc_offset' => null, // TODO
				      'time_zone' => null, // TODO
				      'profile_background_image_url' => null, // not supported by NoseRub
				      'profile_background_tile' => 'false', // not supported by NoseRub
				      'statuses_count' => null, // TODO
				      'notifications' => null, // TODO
				      'verified' => 'false', // we don't have verified users
				      'following' => null,
					  'status' => array('created_at' => date('D M d H:i:s O Y', strtotime($status['Entry']['published_on'])),
									  'id' => $status['Entry']['id'],
									  'text' => $status['Entry']['content'],
									  'source' => '<a href="http://noserub.com">NoseRub</a>', // TODO
									  'truncated' => 'false', // TODO
									  'in_reply_to_status_id' => null, // TODO
  									  'in_reply_to_user_id' => null, // TODO
  									  'favorited' => 'false', // TODO
  									  'in_reply_to_screen_name' => null)); // TODO
  		*/
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
	
	private function respondWithUserNotFound() {
		header("HTTP/1.1 404 Not Found");
	    $this->set('data', array('hash' => array('request' => $this->params['url']['url'], 
	        									 'error' => 'Not found')));
		
	}
}