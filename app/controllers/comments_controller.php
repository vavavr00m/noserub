<?php
class CommentsController extends AppController {
    public $uses = array('Comment');
    public $components = array('api');

    /**
     * shell method to retrieve comments from peers
     */
    public function shell_sync() {
        $admin_hash = isset($this->params['admin_hash']) ? $this->params['admin_hash'] : '';
        
        if($admin_hash != Configure::read('NoseRub.admin_hash') ||
           $admin_hash == '') {
            # there is nothing to do for us here
            return false;
        }
        
        $this->set('data', $this->Comment->sync());
        $this->render('jobs_sync');
    }

    /**
     * public url for shell method shell_sync
     */
    public function cron_sync() {
        $cron_hash= isset($this->params['cron_hash'])  ? $this->params['cron_hash'] : '';
        
        if($cron_hash != Configure::read('NoseRub.cron_hash') ||
           $cron_hash == '') {
            # there is nothing to do for us here
            $this->set('data', __('Value for NoseRub.cron_hash from noserub.php does not match or is empty!', true));
            $this->render('jobs_sync');
            return;
        }
        
        $this->set('data', $this->Comment->sync());
        $this->render('jobs_sync');
    }
}