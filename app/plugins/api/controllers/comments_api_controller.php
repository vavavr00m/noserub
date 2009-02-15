<?php

class CommentsApiController extends ApiAppController {
	public $uses = array('Comment');
	
	/**
	 * return the last 100 comments
	 *
	 * todo: make this configurable by date (eg. get all comments since 2008-12-01 12:34:29)
	 */
	public function get_comments() {
		$this->Comment->contain('Entry', 'Identity');
		$comments = $this->Comment->find(
			'all',
			array(
				'order'  => 'Comment.published_on DESC',
				'limit'  => 100
			)
		);

		$this->set('data', $this->cleanUpData($comments));
		$this->Api->render();
	}
	
	private function cleanUpData(array $comments) {
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
}