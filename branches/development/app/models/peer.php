<?php
/* SVN FILE: $Id:$ */
 
class Peer extends AppModel {
    
	public function getEnabledPeers() {
		$this->contain();
		$peers = $this->find(
            'all',
            array(
                'conditions' => array(
                    'disabled' => '0'
                ),
                'order' => 'last_sync ASC'
            )
        );
        
        return $peers;
	}
	
    /**
     * retrieves a list of peers from http://noserub.com/peers
     * and syncs this list with the ones in the local database
     */
    public function sync() {
        App::import('Vendor', 'WebExtractor');
        $json_data = WebExtractor::fetchUrl('http://noserub.com/peers');
        
        App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));
        Zend_Json::$useBuiltinEncoderDecoder = true;
        $data = Zend_Json::decode($json_data);
        
        if(!$data) {
            return 'ERROR: could not connect to http://noserub.com/peers';
        }
        
        $updated_peers = array();
        foreach($data as $item) {
            if($item['url'] == Configure::read('NoseRub.full_base_url')) {
                # we don't need to set ourselves to that list
                continue;
            }
            
            # check, if we already have this peer
            $this->contain();
            $peer = $this->find(
                'first',
                array(
                    'conditions' => array(
                        'url' => $item['url']
                    )
                )
            );
            
            if(!$peer) {
                # create it!
                $peer = array(
                    'name'      => $item['url'],
                    'url'       => $item['url'],
                    'disabled'  => $item['deleted'],
                    'last_sync' => '2008-01-01 00:00:00'
                );
                $this->create();
                $this->save($peer);
                $updated_peers[] = $item['url'];
            } else {
                # see, if it was disabled. a peer can not be enabled by sync,
                # only disabled, as admin of this installation should be
                # able to disable a peer without it being enabled again
                if($item['deleted'] && !$peer['Peer']['disabled']) {
                    $this->id = $peer['Peer']['id'];
                    $this->saveField('disabled', 1);
                    $updated_peers[] = $item['url'];
                }
            }
        }
        
        if(!$updated_peers) {
            return 'no peers updated';
        }
        
        return $updated_peers;
    }
}