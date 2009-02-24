<?php
class NetworksController extends AppController {
    public $uses = array('Network');
    
    public function index() {
    
    }
    
    public function subscription() {
        if(!$this->data) {
            $this->redirect('/networks/');
            return;
        }
        
        if(!$this->context['logged_in_identity']) {
            $this->redirect('/networks/');
            return;
        }
        
        foreach($this->data['SubscribeNetwork'] as $network_id => $action) {
            $this->Network->id = $network_id;
            switch($action) {
                case -1:
                    $this->Network->unsubscribe($this->context['logged_in_identity']['id']);
                    break;
                    
                case 1:
                    $this->Network->subscribe($this->context['logged_in_identity']['id']);
                    break;
            }
        }
        
        $this->redirect('/networks/');
    }
    
    public function jobs_sync() {
        $this->set('data', $this->Network->sync());
    }
    /**
     * retrieves a list of networks from http://noserub.com/networks
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
     * shell method to retrieve comments and favorites from networks
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