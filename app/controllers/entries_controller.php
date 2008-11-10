<?php
class EntriesController extends AppController {
    public $uses = array('Entry', 'Xmpp');
    public $helpers = array('nicetime', 'flashmessage');
    
    /**
     * Display one entry - permalink for an entry
     *
     * @param int $entry_id
     */
    public function view($entry_id) {
        $this->checkUnsecure();
        
        $session_identity = $this->Session->read('Identity');
        
        $this->Entry->contain(
            'Identity', 'Account', 'ServiceType',
            'FavoritedBy'
        );
        
        $data = $this->Entry->findById($entry_id);
        
        # go through all favorites to load the identity
        # if it's the current identity, set a marker
        foreach($data['FavoritedBy'] as $idx => $item) {
            if($session_identity['id'] == $item['identity_id']) {
                $this->set('already_marked', true);
            }
            $this->Entry->Identity->contain();
            $this->Entry->Identity->id = $item['identity_id'];
            $identity = $this->Entry->Identity->read();
            $data['FavoritedBy'][$idx]['Identity'] = $identity['Identity'];
        }
        $this->set('data', $data);
        $this->set('base_url_for_avatars', $this->Entry->Identity->getBaseUrlForAvatars());
        $this->set('session_identity', $session_identity);
        
        if(isset($data['Identity']['id']) && 
           $session_identity['id'] == $data['Identity']['id']) {
               $this->set('headline', __('Edit your entry', true));
           } else {
               $this->set('headline', __('Permalink', true));
        }
    }
    
    /**
     * delete an entry. only allowed for owner
     *
     * @param int $entry_id
     */
    public function delete($entry_id) {
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        $session_identity = $this->Session->read('Identity');
        if(!isset($session_identity['id']) || !$session_identity['id']) {
            $this->flashMessage('alert', __('You may not delete this entry!', true));
            $this->redirect('/');
        }
        $this->Entry->contain();
        $this->Entry->id = $entry_id;
        $identity_id = $this->Entry->field('identity_id');
        if($identity_id != $session_identity['id']) {
            $this->flashMessage('alert', __('You may not delete this entry!', true));
            $this->redirect('/');
        } else {
            $this->Entry->delete();
            $this->flashMessage('success', __('Entry deleted.', true));
            $this->redirect('/');
        }
    }
    
    /**
     * marks or unmarks an entry as favorite
     *
     * @param int $entry_id
     */
    public function mark($entry_id) {
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        $session_identity = $this->Session->read('Identity');
        if(!isset($session_identity['id']) || !$session_identity['id']) {
            $this->flashMessage('alert', __('You need to be logged in to perform this action!', true));
            $this->redirect('/');
        }
        
        $this->Entry->contain(
            array(
                'FavoritedBy' => array(
                    'conditions' => array(
                        'identity_id' => $session_identity['id']
                    )
                )
            )
        );
        
        $entry = $this->Entry->find(
            'first',
            array(
                'conditions' => array(
                    'Entry.id' => $entry_id,
                    'service_type_id >' => 0 # don't allow NoseRub entries to be marked 
                )
            )
        );
        
        if(!$entry) {
            $this->flashMessage('alert', __('This entry may not be marked!', true));
            $this->redirect('/');
        }
        
        if(isset($entry['FavoritedBy'][0])) {
            # this entry is already marked
            $this->Entry->FavoritedBy->id = $entry['FavoritedBy'][0]['id'];
            $this->Entry->FavoritedBy->delete();
            # todo: remove the NoseRub social stream entry for this
            $this->flashMessage('success', __('Unmarked the entry.', true));
            $this->redirect('/entry/' . $entry_id . '/');
        } else {
            # mark the item
            $data = array(
                'identity_id' => $session_identity['id'],
                'entry_id'    => $entry_id
            );
            $this->Entry->FavoritedBy->create();
            $this->Entry->FavoritedBy->save($data);
            $this->Entry->addFavorite($session_identity['id'], $entry_id);
            $this->flashMessage('success', __('Marked the entry.', true));
            $this->redirect('/entry/' . $entry_id . '/');
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
            $this->set('data', __('Value for NOSERUB_CRON_HASH from noserub.php does not match or is empty!', true));
            $this->render('jobs_update');
            return;
        }
        
        $this->jobs_update();
        $this->render('jobs_update');
    }
    
    public function jobs_update() {
        if(!NOSERUB_MANUAL_FEEDS_UPDATE) {
            $this->set('data', __('NOSERUB_MANUAL_FEEDS_UPDATE in noserub.php not set to do it manually!', true));
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
            $msg = sprintf(__('%d entries added/updated', true), count($entries));
        
            $this->set('data', $msg);
        }
    }
}