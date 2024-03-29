<?php
App::import('Vendor', array('CacheCleaner', 'ConfigurationChecker', 'ExtensionsChecker', 'RandomNumberGeneratorChecker', 'WriteableFoldersChecker'));

class AdminsController extends AppController {
    
    /**
     * Do not include 'Admin' here!
     * Migrations would not be working then!
     */
    public $uses = array('Migration');
    
    public function index() {    
    }
    
    public function password() {
        $this->checkSecure();
        if(!Context::isAdmin()) {
            $this->redirect('/admins/');
        }
        if($this->data && empty($this->params['form']['cancel'])) {
            $this->loadModel('Admin');
            
            $this->Admin->set($this->data);
            $this->Admin->id = Context::read('admin_id');
            
            $password = $this->Admin->field('password');
            if($password != md5($this->data['Admin']['password'])) {
                $this->Admin->invalidate('password', __('Wrong password', true));
                $this->storeFormErrors('Admin', $this->data, $this->Admin->validationErrors);
            } else {
                if($this->Admin->validates()) {
                    $this->data['Admin']['password'] = md5($this->data['Admin']['new_password']);
                    $saveable = array('password');
                    $this->Admin->save($this->data, false, $saveable);
                    $this->redirect('/admins/');
                } else {
                    $this->storeFormErrors('Admin', $this->data, $this->Admin->validationErrors);
                }
            }
            $this->redirect('/admins/password/');
        } else if($this->data) {
            $this->redirect('/admins/');
        }
    }
    
    public function network_settings() {
        $this->checkSecure();
        if(!Context::isAdmin()) {
            $this->redirect('/admins/');
        }
        
        if(!empty($this->data) && empty($this->params['form']['cancel'])) {
            $this->loadModel('Network');
        
            $this->Network->set($this->data);
            $this->Network->id = Context::NetworkId();
            $saveable = array(
                'name', 'url', 'description', 'default_language',
                'latitude', 'longitude', 'google_maps_key',
                'registration_type', 'registration_restricted_hosts',
                'use_ssl', 'api_info_active', 'allow_subscriptions',
				'twitter_consumer_key', 'twitter_consumer_secret'
            );
            if(!$this->Network->save($this->data, true, $saveable)) {
                $this->storeFormErrors('Network', $this->data, $this->Network->validationErrors);
            } else {
                $this->flashMessage('success', __('Network settings saved', true));
                $this->redirect('/admins/');
            }
        } else if($this->data){
            $this->redirect('/admins/');
        }
    }
 	
 	public function user_management() {
        if(!Context::isAdmin()) {
            $this->redirect('/admins/');
        }
    }
 	
    public function login() {
        if(!Context::isLoggedInIdentity()) {
            # you need to be logged in as identity,
            # if you want to gain admin access
            
            # but if there is no identity yet, to which you could log in...
            $this->loadModel('Identity');
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
        
        # not via uses, as /system/update/ will
        # fail, because the admins table is created
        # later on in the process...
        $this->loadModel('Admin');
        
        $admin = $this->Admin->find('first', array(
            'contain' => false,
            'conditions' => array(
                'network_id' => Context::NetworkId(),
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
            $this->layout = 'system_update';
            echo $this->render('/elements/hash_not_valid');
			exit;
        }
    }

	/**
     * Update the system. Check for new migrations
     * and possible new constants. 
     */
    public function system_update() {
		$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $this->set('network_url', 'http://' . $http_host . $this->webroot);
        
        // XXX security_token is set to avoid "undefined variable" error in view
        $this->set('security_token', '');
        $this->set('extensions', ExtensionsChecker::check());
		$this->set('random_number_generator', RandomNumberGeneratorChecker::check());
        
        $this->layout = 'system_update';
        
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
