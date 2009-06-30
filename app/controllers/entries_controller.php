<?php
class EntriesController extends AppController {
    public $uses = array('Entry', 'Xmpp');
    public $helpers = array('nicetime');
    
    
    /**
     * Adding an entry
     */
    public function add() {
        $this->grantAccess('guest');
        
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
            
            if($this->Entry->add(
                $this->data['Entry']['service_type'], 
                $this->data['Entry'], 
                Context::loggedInIdentityId(),
                $this->data['Entry']['group_id'], 
                null)) {
                    
                $this->flashMessage('success', __('New entry was created.', true));
            } else {
                $this->flashMessage('error', __('New entry could not be created.', true));
            }
        } else {
            $is_group = isset($this->params['named']['is_group']) ? $this->params['named']['is_group'] : false;
            if(isset($this->params['named']['modus']) && !$is_group) {
                $this->Session->write('entry_add_modus', $this->params['named']['modus']);
            } else if(isset($this->params['named']['modus']) && $is_group) {
                $this->Session->write('entry_group_add_modus', $this->params['named']['modus']);
            }
        }
        
        $this->redirect($this->referer());
    }
    
    /**
     * This is the page /:username/activities
     */
    public function profile() {
        Context::setPage('profile.activities');
    }
    
    /**
     * Display one entry - permalink for an entry
     *
     * @param int $entry_id
     */
    public function view($entry_id) {
        $this->checkUnsecure();
        
        Context::write('entry.id', $entry_id);

        // check, if this belongs to a group
        $this->Entry->id = $entry_id;
        $group_id = $this->Entry->field('group_id');    
        
        if($group_id && empty($this->params['slug'])) {
            // this entry belongs to a group, but
            // the URL has no group slug
            $this->Entry->Group->id = $group_id;
            $slug = $this->Entry->Group->field('slug');
            $this->redirect('/groups/entry/' . $slug . '/' . $entry_id);
        }
        
        if($group_id) {
            $this->set(
                'group', 
                $this->Entry->Group->find('first', array(
                    'contain' => false,
                    'conditions' => array(
                        'Group.id' => $group_id
                    )
                ))
            );
            
            $this->render('group_view');
        } else {
            $this->render('view');
        }
    }
    
    /**
     * delete an entry. only allowed for owner
     *
     * @param int $entry_id
     */
    public function delete($entry_id) {
        # deleting entries is a little bit tricky, when we have no
        # confirmation, which accounts belong to which users: two users
        # cann add the same RSS Feed and then both would be able to
        # delete items. Who do those entries really belong to?
        $this->flashMessage('alert', __('You may not delete this entry!', true));
        $this->redirect('/entry/' . $entry_id);
        return;
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        $session_identity = $this->Session->read('Identity');
        if(!isset($session_identity['id']) || !$session_identity['id']) {
            $this->flashMessage('alert', __('You may not delete this entry!', true));
            $this->redirect('/entry/' . $entry_id);
        }
        $this->Entry->contain();
        $this->Entry->id = $entry_id;
        $identity_id = $this->Entry->field('identity_id');
        if($identity_id != $session_identity['id']) {
            $this->flashMessage('alert', __('You may not delete this entry!', true));
            $this->redirect('/entry/' . $entry_id);
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
            
            App::import('Model', 'Mail');
            $Mail = new Mail();
            $Mail->notifyFavorite($session_identity['id'], $entry_id);
            
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
        
        if($cron_hash != Configure::read('NoseRub.cron_hash') ||
           $cron_hash == '') {
            # there is nothing to do for us here
            $this->set('data', __('Value for NoseRub.cron_hash from noserub.php does not match or is empty!', true));
            $this->render('jobs_update');
            return;
        }
        
        $this->jobs_update();
        $this->render('jobs_update');
    }
    
    public function jobs_update() {
        if(!Configure::read('NoseRub.manual_feeds_update')) {
            $this->set('data', __('NoseRub.manual_feeds_update in noserub.php not set to do it manually!', true));
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