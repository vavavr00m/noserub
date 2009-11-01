<?php
   
class NetworkController extends ApiAppController {	
	const MAX_RECENT_COMMENTS = 100;
	public $components = array('Cluster');
	
	/**
	 * return the last 100 comments
	 *
	 * todo: make this configurable by date (eg. get all comments since 2008-12-01 12:34:29)
	 */
	public function comments() {
		$this->loadModel('Comment');
		$comments = $this->Comment->getRecent(self::MAX_RECENT_COMMENTS);

		$this->set('data', array('data' => $this->formatComments($comments)));
	}
		
	/**
	 * return the last 100 favorites
	 *
	 * todo: make this configurable by date (eg. get all favorites since 2008-12-01 12:34:29)
	 */
	public function favorites() {
		$this->loadModel('Favorite');
		$this->Favorite->Entry = ClassRegistry::init('Entry');
		
		$this->Favorite->contain();
		$favorites = $this->Favorite->find(
			'all',
			array(
				'fields' => 'Favorite.entry_id',
				'order'  => 'Favorite.created DESC',
				'limit'  => 100
			)
		);
		$entry_ids = Set::extract($favorites, '{n}.Favorite.entry_id');
		$conditions = array('entry_id' => $entry_ids);
		$items = $this->Favorite->Entry->getForDisplay($conditions, 100, true);
        
        usort($items, 'sort_items');
        $items = $this->Cluster->removeDuplicates($items);

		$this->set('data', array('data' => $this->formatFavorites($items)));
	}
	
	/**
     * used to return number of registered, active users, and some other
     * values.
     */
    public function info() {
        $data = array();
    	
    	if (Context::read('network.api_info_active')) {
    		$this->loadModel('Identity');
    		$this->loadModel('Migration');
    		
            $restricted_hosts = Context::read('network.registration_restricted_hosts');
            $data = array(
                'num_users' => $this->Identity->Network->getNumberOfUsers(Context::read('network.id')),
                'registration_type' => Context::read('network.registration_type'),
                'restricted_hosts'  => $restricted_hosts ? true : false,
                'migration' => $this->Migration->getCurrentMigration(),
                'allow_subscriptions' => Context::read('network.allow_subscriptions') ? true : false
            );
        } else {
        	$data = array('data' => array('error' => 'Info-API is disabled'));
        	header("HTTP/1.0 503 Service Unavailable");
        }
        
        $this->set('data', array('data' => $data));
    }
    
	private function formatComments(array $comments) {
		$data = array();
		foreach($comments as $comment) {
			if($comment['Entry']['url']) {
				$url = $comment['Entry']['url'];
			} else {
				$url = Router::url('/entry/' . $comment['Entry']['id'], true);
			}
			
			if($comment['Entry']['uid']) {
				$uid = $comment['Entry']['uid'];
			} else {
				$uid = md5($url);
			}
			
			$data[] = array(
				'uid'          => $uid,
				'url'          => $url,
				'commented_by' => $comment['Identity']['username'],
				'commented_on' => $comment['Comment']['published_on'],
				'comment'      => $comment['Comment']['content']
			);
		}
		
		return $data;
	}
	
	private function formatFavorites(array $items) {
		$data = array();
		foreach($items as $item) {
			foreach($item['FavoritedBy'] as $favorited_by) {
				$data[] = array(
					'uid'          => $item['Entry']['uid'],
					'url'          => $item['Entry']['url'],
					'favorited_by' => $favorited_by['Identity']['username'],
					'favorited_on' => $favorited_by['created'] 
				);
			}
		}
		
		return $data;
	}
}