<?php

class FavoritesController extends ApiAppController {
	public $uses = array('Favorite');
	public $components = array('Cluster');
	
	/**
	 * return the last 100 favorites
	 *
	 * todo: make this configurable by date (eg. get all favorites since 2008-12-01 12:34:29)
	 */
	public function recent_favorites() {
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

		$this->set('data', $this->formatData($items));
	}
	
	private function formatData(array $items) {
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