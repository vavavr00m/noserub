<?php
/* SVN FILE: $Id:$ */
 
class Comment extends AppModel {
    public $belongsTo = array('Entry', 'Identity');                                                   

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
        
        # get uid of that Entry
        $original_entry_id = $data['entry_id'];
        $this->Entry->id = $original_entry_id;
        $uid = $this->Entry->field('uid');
        
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
            }
        }
    }
    
    /**
     * poll new comments from peers
     */
    public function poll() {
        App::import('Model', 'Peer');
        App::import('Model', 'Mail');
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        App::import('Vendor', 'WebExtractor');
        
        $Mail = new Mail();
        
        $Peer = new Peer();
        $peers = $Peer->getEnabledPeers();
        
        $polled = array();
        Zend_Json::$useBuiltinEncoderDecoder = true;
        
        foreach($peers as $peer) {
            $json_data = WebExtractor::fetchUrl($peer['Peer']['url'] . '/api/json/comments/');
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
            
            $polled[] = sprintf(__('%d comments from %s, %d imported', true), count($comments['data']), $peer['Peer']['name'], $imported);
        }
        
        return $polled;
    }
}