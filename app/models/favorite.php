<?php
/* SVN FILE: $Id:$ */
 
class Favorite extends AppModel {
    public $belongsTo = array(
        'Identity', 
        'Entry' => array('counterCache' => true)
    );

    /**
     * poll new favorites from networks
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
            $json_data = WebExtractor::fetchUrl($network['Network']['url'] . '/api/json/favorites/');
            $favorites = Zend_Json::decode($json_data);
            if(!isset($favorites['data']) || !is_array($favorites['data'])) {
                $favorites = array('data' => array());
                $imported = 0;
            } else {
                $imported = 0;
                foreach($favorites['data'] as $favorite) {
                    $entries = $this->Entry->getByUid($favorite['uid'], $favorite['url']);
                    if($entries) {
                        # we have this entry, now get the identity
                        $identity_id = $this->Identity->getIdForUsername($favorite['favorited_by']);
                        
                        foreach($entries as $entry) {
                            # check, if we already have this comment
                            $conditions = array(
                                'entry_id'    => $entry['Entry']['id'],
                                'identity_id' => $identity_id
                            );

                            if(!$this->hasAny($conditions)) {
                                # we need to create it
                                $data = $conditions;
                                $data['created'] = $favorite['favorited_on'];
                                $this->create();
                                $this->save($data);
                                $imported++;
                                
                                $Mail->notifyFavorite($identity_id, $entry['Entry']['id']);
                            }
                        }
                    }
                }
            }
            
            $polled[] = sprintf(__('%d favorites from %s, %d imported', true), count($favorites['data']), $network['Network']['name'], $imported);
        }
        
        return $polled;
    }
}