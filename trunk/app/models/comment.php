<?php
/* SVN FILE: $Id:$ */
 
class Comment extends AppModel {
    public $belongsTo = array(
        'Entry' => array('counterCache' => true),
        'Identity'
    );                                                   

    /**
     * Creates the given comment and also checks, if other entries
     * with this uid exist and add comments to it, too.
     */
    public function createForAll($data) {
        App::import('Model', 'Mail');
        $Mail = new Mail();
        
        $this->create();
        $this->save($data);
        $Mail->notifyComment($data['identity_id'], $data['entry_id'], $data['content']);
        $this->Entry->id = $data['entry_id'];
        $this->Entry->updateLastActivity();
        
        $original_entry_id = $data['entry_id'];
        $uid = $this->Entry->getUid($original_entry_id);
        
        if($uid) {
            # get all entries with this uid
            $entries = $this->Entry->getByUid($uid);
            foreach($entries as $entry) {
                if($entry['Entry']['id'] == $original_entry_id) {
                    # we already added this comment
                    continue;
                }
                # add comment to this entry
                $data['entry_id'] = $entry['Entry']['id'];
                $this->create();
                $this->save($data);
            
                $Mail->notifyComment($entry['Entry']['identity_id'], $data['entry_id'], $data['content']);
                $this->Entry->id = $data['entry_id'];
                $this->Entry->updateLastActivity();
            }
        }
    }
    
    /**
     * Deletes all comments for given entry_id
     *
     * @param  $entry_id for which all entries should be removed
     * @return 
     * @access 
     */
    public function deleteByEntryId($entry_id) {
        $this->contain();
        return $this->deleteAll(array('Comment.entry_id' => $entry_id));
    }
    
    public function getRecent($limit) {
    	$this->contain('Entry', 'Identity');
		
    	return $this->find('all', array('order' => 'Comment.published_on DESC', 
    									'limit' => $limit));
    }
    
    /**
     * poll new comments from networks
     */
    public function poll() {
        App::import('Model', 'Network');
        App::import('Model', 'Mail');
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        App::import('Vendor', 'WebExtractor');
        
        $Mail = new Mail();
        
        $Network = new Network();
        $networks = $Network->getEnabled();
        
        $polled = array();
        
        foreach($networks as $network) {
            $json_data = WebExtractor::fetchUrl($network['Network']['url'] . '/api/network/comments.json');
            $comments = Zend_Json::decode($json_data);
            if(!isset($comments['data']) || !is_array($comments['data'])) {
                $comments = array('data' => array());
                $imported = 0;
            } else {
                $imported = 0;
                foreach($comments['data'] as $comment) {
                    $entries = $this->Entry->getByUid($comment['uid'], $comment['url']);
                    if($entries) {
                        # we have this entry, now get the identity
                        $identity_id = $this->Identity->getIdForUsername($comment['commented_by']);
                        
                        # add comment to the entries
                        foreach($entries as $entry) {
                            # check, if we already have this comment
                            $conditions = array(
                                'entry_id'    => $entry['Entry']['id'],
                                'identity_id' => $identity_id,
                                'content'     => $comment['comment']
                            );
                            
                            if(!$this->hasAny($conditions)) {
                                # we need to create it
                                $data = $conditions;
                                $data['published_on'] = $comment['commented_on'];
                                $this->create();
                                $this->save($data);
                                $imported++;
                                
                                $Mail->notifyComment($identity_id, $entry['Entry']['id'], $comment['comment']);
                            }
                        }
                    }
                }
            }
            
            $polled[] = sprintf(__('%d comments from %s, %d imported', true), count($comments['data']), $network['Network']['name'], $imported);
        }
        
        return $polled;
    }
}