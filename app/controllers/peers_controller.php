<?php
class PeersController extends AppController {
    public $uses = array('Peer');
    

    public function jobs_sync() {
        $this->set('data', $this->Peer->sync());
    }
    /**
     * retrieves a list of peers from http://noserub.com/peers
     * and syncs this list with the ones in the local database
     */
    public function shell_sync() {
        $this->jobs_sync();
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
        
        $this->jobs_sync();
        $this->render('jobs_sync');
    }
    
    public function jobs_poll() {
        App::import('Model', 'Comment');
        App::import('Model', 'Favorite');
        $Comment = new Comment();
        $Favorite = new Favorite();
        
        $comments  = $Comment->poll();
        $favorites = $Favorite->poll();
        $this->set(compact('comments', 'favorites'));
    }

    /**
     * shell method to retrieve comments and favorites from peers
     */
    public function shell_poll() {
        $this->params['admin_hash'] = Configure::read('NoseRub.admin_hash');
        $this->jobs_poll();
        $this->render('jobs_poll');
    }

    /**
     * public url for shell method shell_sync
     */
    public function cron_poll() {
        $cron_hash= isset($this->params['cron_hash'])  ? $this->params['cron_hash'] : '';
        
        if($cron_hash != Configure::read('NoseRub.cron_hash') ||
           $cron_hash == '') {
            # there is nothing to do for us here
            $this->set('data', __('Value for NoseRub.cron_hash from noserub.php does not match or is empty!', true));
            $this->render('jobs_poll');
            return;
        }
        
        $this->jobs_poll();
        $this->render('jobs_poll');
    }
}