<?php
/* SVN FILE: $Id:$ */
 
class Favorite extends AppModel {
    public $belongsTo = array('Identity', 'Entry');

    /**
     * poll new favorites from peers
     */
    public function poll() {
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
        
        $polled = array();
        foreach($peers as $peer) {
            $json_data = WebExtractor::fetchUrl($peer['Peer']['url'] . '/api/json/favorites/');
            $zend_json = new Zend_Json();
            $zend_json->useBuiltinEncoderDecoder = true;
            $favorites = $zend_json->decode($json_data);
            if(!isset($favorites['data']) || !is_array($favorites['data'])) {
                $favorites = array();
                $imported = 0;
            } else {
                $imported = 0;
                foreach($favorites['data'] as $favorite) {
                    $entry = $this->Entry->getByUid($favorite['uid'], $favorite['url']);
                    if($entry) {
                        # we have this entry, nw get the identity
                        $identity_id = $this->Identity->getIdForUsername($favorite['favorited_by']);
                        # check, if we already have this comment
                        $conditions = array(
                            'entry_id'    => $entry['Entry']['id'],
                            'identity_id' => $identity_id
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
                            $data['created'] = $favorite['favorited_on'];
                            $this->create();
                            $this->save($data);
                            $imported++;
                        }
                    }
                }
            }
            
            $polled[] = sprintf(__('%d favorites from %s, %d imported', true), count($favorites), $peer['Peer']['name'], $imported);
        }
        
        return $polled;
    }
}