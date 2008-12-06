<?php
/* SVN FILE: $Id:$ */
 
class Comment extends AppModel {
    public $belongsTo = array('Entry', 'Identity');                                                   

    /**
     * retrieving new comments from peers
     */
    public function sync() {
        App::import('Model', 'Peer');
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        App::import('Vendor', 'WebExtractor');
        
        $Peer = new Peer();
        
        $Peer->contain();
        $peers = $Peer->find(
            'all',
            array(
                'conditions' => array(
                    'disabled' => '0'
                ),
                'order' => 'last_sync ASC'
            )
        );
        
        $synced = array();
        foreach($peers as $peer) {
            $json_data = WebExtractor::fetchUrl($peer['Peer']['url'] . '/api/json/comments/');
            $zend_json = new Zend_Json();
            $zend_json->useBuiltinEncoderDecoder = true;
            $comments = $zend_json->decode($json_data);
            if(!isset($comments['data']) || !is_array($comments['data'])) {
                $comments = array();
                $imported = 0;
            } else {
                $imported = 0;
                foreach($comments['data'] as $comment) {
                    $entry = $this->Entry->getByUid($comment['uid'], $comment['url']);
                    if($entry) {
                        # we have this entry, nw get the identity
                        $identity_id = $this->Identity->getIdForUsername($comment['commented_by']);
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
            
            $synced[] = sprintf(__('%d comments from %s, %d imported', true), count($comments), $peer['Peer']['name'], $imported);
        }
        
        return $synced;
    }
}
