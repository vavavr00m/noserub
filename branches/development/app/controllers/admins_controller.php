<?php
 
class AdminsController extends AppController {
    public $uses = array('Admin');
    
    public function beforeFilter() {
    	parent::beforeFilter();
    	$admin_hash = isset($this->params['admin_hash']) ? $this->params['admin_hash'] : '';
        
        if($admin_hash != NOSERUB_ADMIN_HASH || $admin_hash == '') {
            # there is nothing to do for us here
            echo $this->render('/elements/hash_not_valid');
			exit;
        }
    }

	/**
     * Update the system. Check for new migrations
     * and possible new constants. 
     */
    public function system_update() {
        $this->set('extensions', $this->Admin->checkExtensions());
        
        $directories = $this->Admin->checkWriteable();
        $this->set('directories', $directories);
        if(!empty($directories)) {
            return;
        }
        
        $constants = $this->Admin->checkConstants();
        $this->set('constants', $constants);
        if(!empty($constants)) {
            return;
        }
        
        $database_status = $this->Admin->getDatabaseStatus();
        $this->set('database_status', $database_status);
        if($database_status == 1) {
            $current_migration     = $this->Admin->getCurrentMigration();
            $most_recent_migration = $this->Admin->getMostRecentMigration();
            $this->set('current_migration', $current_migration);
            $this->set('most_recent_migration', $most_recent_migration);
            
            if($current_migration < $most_recent_migration) {
                $migrations = $this->Admin->getOpenMigrations($current_migration);
                $this->Admin->migrate($migrations, $current_migration, $most_recent_migration);
            
                $this->set('migrations', $migrations);
            }
        }
    }
}