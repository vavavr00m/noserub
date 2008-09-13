<?php
class EntriesController extends AppController {
    public $uses = array('Entry', 'Xmpp');
    
    /**
     * Go through all accounts and update
     * the entries.
     *
     * @param  
     * @return 
     * @access 
     */
    public function shell_update() {
        $this->jobs_update();       
    } 

    public function jobs_update() {
        $this->Entry->Account->contain();
        $data = $this->Entry->Account->find(
            'all',
            array(
                'fields'     => 'id',
                'conditions' => array(
                    'next_update <= NOW()'
                )
            )
        );

        $entries = array();
        foreach($data as $item) {
            $new_entries = $this->Entry->updateByAccountId($item['Account']['id']);
            if($new_entries) {
                $entries = array_merge($entries, $new_entries);
            }
        }
        $messages = array();
        foreach($entries as $entry) {
            if(!$entry['restricted']) {
                $messages[] = $this->Entry->getMessage($entry);
            }
        }
        $this->Xmpp->broadcast($messages);
        $msg = count($entries) . ' entries added/updated';
        
        $this->set('data', $msg);
    }
}