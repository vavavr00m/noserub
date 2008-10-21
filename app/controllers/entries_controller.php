<?php
class EntriesController extends AppController {
    public $uses = array('Entry', 'Xmpp');
    public $helpers = array('nicetime');
    
    /**
     * Display one entry - permalink for an entry
     *
     * @param int $entry_id
     */
    public function view($entry_id) {
        $this->checkUnsecure();
        
        $session_identity = $this->Session->read('Identity');
        
        $this->Entry->contain('Identity', 'Account', 'ServiceType');
        $data = $this->Entry->findById($entry_id);
        $this->set('data', $data);
        $this->set('base_url_for_avatars', $this->Entry->Identity->getBaseUrlForAvatars());
        
        if(isset($data['Identity']['id']) && 
           $session_identity['id'] == $data['Identity']['id']) {
               $this->set('headline', 'Edit your entry');
           } else {
               $this->set('headline', 'Permalink');
        }
    }
    
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
        $this->render('jobs_update');
    } 

    public function cron_update() {
        $cron_hash= isset($this->params['cron_hash'])  ? $this->params['cron_hash'] : '';
        
        if($cron_hash != NOSERUB_CRON_HASH ||
           $cron_hash == '') {
            # there is nothing to do for us here
            $this->set('data', 'Value for NOSERUB_CRON_HASH from noserub.php does not match or is empty!');
            $this->render('jobs_update');
            return;
        }
        
        $this->jobs_update();
        $this->render('jobs_update');
    }
    
    public function jobs_update() {
        if(!NOSERUB_MANUAL_FEEDS_UPDATE) {
            $this->set('data', 'NOSERUB_MANUAL_FEEDS_UPDATE in noserub.php not set to do it manually!');
        } else {
            $this->Entry->Account->contain();
            $data = $this->Entry->Account->find(
                'all',
                array(
                    'fields'     => 'id',
                    'conditions' => array(
                        'next_update <= NOW()'
                    ),
                    'limit' => 50,
                    'order' => 'next_update ASC'
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
}