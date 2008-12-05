<?php
class PeersController extends AppController {
    public $uses = array('Peer');
    
    /**
     * retrieves a list of peers from http://noserub.com/peers
     * and syncs this list with the ones in the local database
     */
    public function shell_sync() {
        $admin_hash = isset($this->params['admin_hash']) ? $this->params['admin_hash'] : '';
        
        if($admin_hash != Configure::read('NoseRub.admin_hash') ||
           $admin_hash == '') {
            # there is nothing to do for us here
            return false;
        }
        
        $data = $this->Peer->sync();
        
        $this->set('data', $data);
        
        $this->render('jobs_sync');
    }
    
    public function cron_sync() {
        $cron_hash = isset($this->params['cron_hash'])  ? $this->params['cron_hash'] : '';
        
        if($cron_hash != Configure::read('NoseRub.cron_hash') ||
           $cron_hash == '') {
            # there is nothing to do for us here
            $this->set('data', __('Value for NoseRub.cron_hash from noserub.php does not match or is empty!', true));
            $this->render('jobs_sync');
            return;
        }
        
        $data = $this->Peer->sync();
        
        $this->set('data', $data);
        
        $this->render('jobs_sync');
    }
}