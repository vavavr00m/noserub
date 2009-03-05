<?php
App::import('Vendor', array('CacheCleaner', 'ConfigurationChecker', 'ExtensionsChecker', 'WriteableFoldersChecker'));

class AdminsController extends AppController {
    public $uses = array('Admin', 'Migration');
    
    public function index() {
        
    }
    
    public function login() {
        if(!Configure::read('context.logged_in_identity')) {
            # you need to be logged in as identity,
            # if you want to gain admin access
            
            # but if there is no identity yet, to which you could log in...
            $this->dynamicUse('Identity');
            if($this->Identity->isIdentityAvailableForLogin()) {
                $this->redirect('/admins/');
                return;
            }
        }
        
        if(!$this->data) {
            # don't call this route directly
            $this->redirect('/admins/');
            return;
        }
        
        $admin = $this->Admin->find('first', array(
            'contain' => false,
            'conditions' => array(
                'network_id' => Configure::read('context.network.id'),
                'username' => $this->data['Admin']['username'],
                'password' => md5($this->data['Admin']['password']),
            )
        ));
        
        if($admin) {
            $this->Admin->id = $admin['Admin']['id'];
            $this->Admin->saveField('last_login', date('Y-m-d H:i:s'));
            $this->Session->write('Admin.id', $admin['Admin']['id']);
            $this->redirect('/admins/');
        } else {
            $this->redirect('/admins');
        }
    }
    
    public function logout() {
        $this->ensureSecurityToken();
        $this->Session->delete('Admin');
        $this->redirect('/admins/');
    }
    
    public function beforeFilter() {
        parent::beforeFilter();
        
        if(defined('SHELL_DISPATCHER') && SHELL_DISPATCHER) {
            return;
        }
        
        if(!isset($this->params['admin_hash'])) {
            # this is not a admin_hash route
            return;
        } else 
        
    	$admin_hash = $this->params['admin_hash'];
        
        if($admin_hash != Configure::read('NoseRub.admin_hash') || $admin_hash == '') {
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
    	// XXX security_token is set to avoid "undefined variable" error in view
    	$this->set('security_token', '');
        $this->set('extensions', ExtensionsChecker::check());
        
        $directories = WriteableFoldersChecker::check();
        $this->set('directories', $directories);
        if(!empty($directories)) {
            return;
        }
        
        CacheCleaner::cleanUp();
        
        $this->ConfigurationChecker = new ConfigurationChecker();
        $constants = $this->ConfigurationChecker->check();
        $this->set('constants', $constants);
        if(!empty($constants)) {
            return;
        }
        
        $database_status = $this->Migration->getDatabaseStatus();
        $this->set('database_status', $database_status);
        if($database_status == 1) {
            $current_migration     = $this->Migration->getCurrentMigration();
            $most_recent_migration = $this->Migration->getMostRecentMigration();
            $this->set('current_migration', $current_migration);
            $this->set('most_recent_migration', $most_recent_migration);
            
            if($current_migration < $most_recent_migration) {
                $migrations = $this->Migration->getOpenMigrations($current_migration);
                $this->Migration->migrate($migrations, $current_migration, $most_recent_migration);
            
                $this->set('migrations', $migrations);
            }
        }
    }
    
    /**
     * Enables to call /system/update from the shell.
     * Will look ugly, but the primary goal is to be able
     * to call this without a timeout.
     */
    public function shell_system_update() {
        $this->system_update();
        $this->render('system_update');
    }
}