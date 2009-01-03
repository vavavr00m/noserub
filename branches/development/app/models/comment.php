<?php
/* SVN FILE: $Id:$ */
 
class Comment extends AppModel {
    public $belongsTo = array('Entry', 'Identity');                                                   

    /**
     * Creates the given comment and also checks, if other entries
     * with this uid exist and add comments to it, too.
     */
    public function createForAll($data) {
        $this->create();
        $this->save($data);
        
        # get uid of that Entry
        $this->Entry->id = $data['entry_id'];
        $uid = $this->Entry->field('uid');
        
        # get all entries with this uid
        $entries = $this->Entry->getByUid($uid);
        foreach($entries as $entry) {
            if($entry['Entry']['id'] == $this->Entry->id) {
                # we already added this comment
                continue;
            }
            # add comment to this entry
            $data['entry_id'] = $entry['Entry']['id'];
            $this->create();
            $this->save($data);
        }
    }
    
    /**
     * poll new comments from peers
     */
    public function poll() {
        App::import('Model', 'Peer');
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        App::import('Vendor', 'WebExtractor');
        
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
                            $this->contain();
                            $count = $this->find(
                                'count',
                                array(
                                    'conditions' => $conditions
                                )
                            );
                            if(!$count) {
                                # we need to create it
                                $data = $conditions;
                                $data['published_on'] = $comment['commented_on'];
                                $this->create();
                                $this->save($data);
                                $imported++;
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