<?php
/* Part of the twitter-compatible API */
class StatusesController extends ApiAppController {
	
	public function public_timeline() {
		$this->loadModel('Entry');
		$this->set('data', array('statuses' => $this->formatStatuses($this->Entry->getForDisplay(array(), 20, false))));
	}
	
	private function formatStatuses(array $statuses) {
		$data = array();
		
		foreach ($statuses as $status) {
			$data[] = array('status' => array('created_at' => date('D M d H:i:s O Y', strtotime($status['Entry']['published_on'])),
											  'id' => $status['Entry']['id'],
											  'text' => $status['Entry']['content'],
											  'source' => '<a href="http://noserub.com">NoseRub</a>', // TODO
											  'truncated' => 'false', // TODO
											  'in_reply_to_status_id' => null, // TODO
  											  'in_reply_to_user_id' => null, // TODO
  											  'favorited' => 'false', // TODO
  											  'in_reply_to_screen_name' => null, // TODO
											  'user' => array('id' => $status['Entry']['identity_id'],
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
														      'following' => null))); // TODO
		}
		
		return $data;
	}
}