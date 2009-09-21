<?php

class ArrayFactory {
	
	public static function status_with_user($data) {
		$status = array('status' => ArrayFactory::status($data));
		$status['status']['user'] = ArrayFactory::user($data);
		
		return $status;
	}
	
	public static function user_with_status($data) {
		$user = array('user' => ArrayFactory::user($data));
		$user['user']['status'] = ArrayFactory::status($data);
		
		return $user;
	}
	
	private static function status($data) {
		return array('created_at' => date('D M d H:i:s O Y', strtotime($data['Entry']['published_on'])),
					 'id' => $data['Entry']['id'],
					 'text' => $data['Entry']['content'],
					 'source' => '<a href="http://noserub.com">NoseRub</a>', // TODO
					 'truncated' => 'false', // TODO
					 'in_reply_to_status_id' => null, // TODO
  					 'in_reply_to_user_id' => null, // TODO
  					 'favorited' => 'false', // TODO
  					 'in_reply_to_screen_name' => null);
	}
	
	private static function user($data) {
		return array('id' => $data['Entry']['identity_id'],
					 'name' => $data['Identity']['firstname'] . ' ' . $data['Identity']['lastname'], // TODO
					 'screen_name' => $data['Identity']['single_username'],
				     'location' => null, // TODO
				     'description' => null, // TODO
				     'profile_image_url' => $data['Identity']['photo'],
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
				     'following' => null); // TODO
	}
}