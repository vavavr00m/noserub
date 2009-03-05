<?php
/**
 * AppController - application wide controller
 *
 * Base class for all controllers.
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package    profiles
 * @subpackage controllers
 */
class AppController extends Controller {
    public $helpers = array('noserub', 'javascript', 'html', 'form');
    public $components = array('menu', 'Cookie');
    public $view = 'Theme';
    public $theme = 'default';
    
    /**
     * Never ever "use" something here, or the migrations will fail.
     * See Issue #127
     * http://code.google.com/p/noserub/issues/detail?id=127
     *
     * If you need a specific model in AppController, use "App::import('Model', 'ModelName');"
     *
     * (Basically no models that make use of a database table may be 'used' here.
     *  Because if you do so, Cake will complain and you have no chance of doing
     *  the migrations in /system/update.)
     */
    public $uses = array(); 
    
    public function flashMessage($type, $message) {
        $flash_messages = $this->Session->read('FlashMessages');
        $flash_messages[$type][] = $message;
        $this->Session->write('FlashMessages', $flash_messages);
    }
    
    /**
     * Makes sure we redirect to the https url,
     * when context.network.use_ssl is used and we're not
     * on a secure page
     */
    public function checkSecure() {
        if(defined('SHELL_DISPATCHER')) {
            return;
        }
        
        if(Configure::read('context.network.use_ssl')) {
            $server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 0;
            if($server_port != 443) {
                $this->redirect(str_replace('http://', 'https://', FULL_BASE_URL) . $this->here);
            }
        }
    }
    
    /**
     * Makes sure we redirect to the http url,
     * when we're not on a secure page
     */
    public function checkUnsecure() {
        if(defined('SHELL_DISPATCHER')) {
            return;
        }
        
        $server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 0;
        if($server_port != 80) {
            $this->redirect(str_replace('https://', 'http://', FULL_BASE_URL) . $this->here);
        }
    }

    public function beforeFilter() {
        if(!$this->isSystemUpdatePage()) {
            # check for auto-login
            if(!$this->Session->check('Identity.id')) {
                $this->autoLogin();
            }
        }
        
        $session_theme = $this->Session->read('theme');
        if(isset($this->params['url']['theme'])) {
            $session_theme = $this->params['url']['theme'];
            $this->Session->write('theme', $session_theme);
        }
        if($session_theme) {
            $this->theme = $session_theme;
        }
        
        # Localization
        App::import('Core', 'l10n');
        $this->L10n = new L10n();
        # if language is already set in session, get that
        $language = $this->Session->read('Config.language');
        if(!$language) {
            # if not, get NoseRub default language and save it
            # in the session
            $language = Configure::read('context.network.default_language');
            $this->Session->write('Config.language', $language);
        }
        # now set the language
        $this->L10n->get($language);

        setlocale(LC_ALL, 
            substr($this->L10n->locale, 0, 3) .
            strtoupper(substr($this->L10n->locale, 3, 2)) . 
            '.' . $this->L10n->charset
        );
        
        # set menu data
        $this->menu->setViewData($this);  
        
        /**
         * 	Don't you EVER remove this line else you will make the whole 
         * 	application a swiss cheese for XSS!
         *  We often call echo $this->here in our form actions and this would
         *  be exactly where the injection would take place.
         */
        $this->here = preg_replace('/("|\')/', '”', addslashes(strip_tags($this->here)));
        
        if(defined('SHELL_DISPATCHER')) {
            $this->layout = 'shell';
            if($this->action != '' &&
               ($this->name . '.' . $this->action) != 'Mails.send' &&
               strpos($this->action, 'shell_') !== 0) {
                echo __('You may not call this route from the shell!', true) . "\n";
                echo $this->name . '.' . $this->action . "\n\n";
                exit;
            }
        } else {
            if(strpos($this->action, 'shell_') === 0) {
                echo '<h1>' . __('Error', true) . '</h1><p>' . __('This route is only accessible from the shell!', true) . '</p>';
                exit;
            }
        }
        
        $this->updateContext();
    }
    
    public function ensureSecurityToken() {
        if(!isset($this->Identity)) {
            App::import('Model', 'Identity');
            $this->Identity = new Identity();
        }
        $session_identity_id = $this->Session->read('Identity.id');
        if(isset($this->params['form']['security_token'])) {
            # POST
            $security_token = $this->params['form']['security_token'];
        } else {
            # GET
            $security_token = isset($this->params['security_token']) ? $this->params['security_token'] : '';
            if(!$security_token) {
                $security_token = isset($this->params['named']['_t']) ? $this->params['named']['_t'] : '';
            }
        }
        
        if(!$this->Identity->isCorrectSecurityToken($session_identity_id, $security_token)) {
            $this->redirect('/pages/security_check/', null, true);
        }
    }
    
    public function beforeRender() {
        if($this->viewPath != 'errors' && !$this->isSystemUpdatePage()) {
	        if(!isset($this->Identity)) {
	            App::import('Model', 'Identity');
	            $this->Identity = new Identity();
	        }
	        # set new security_token
	        $this->set('security_token', $this->Identity->updateSecurityToken($this->Session->read('Identity.id')));
        }
    }
    
    public function afterFilter() {
        $this->Session->write('FlashMessages', array());
    }
    
    protected function updateContext() {
        if($this->isSystemUpdatePage()) {
            return;
        }
        
        if(!isset($this->Network)) {
            App::import('Model', 'Network');
            $this->Network = new Network;
        }
        
        # get the network data. right now, always
        # for network_id 1
        $data = $this->Network->find('first', array(
            'contain' => false,
            'conditions' => array('id' => 1)
        ));
        
        if(!$data['Network']['url']) {
            # when no URL is found, we try to guess it
            $http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            $data['Network']['url'] = 'http://' . $http_host . $this->webroot;
        }
        
        Configure::write('context.network', $data['Network']);

        Configure::write('context.logged_in_identity', $this->Session->read('Identity'));
        
        if($this->Session->read('Admin.id')) {
            Configure::write('context.admin_id', $this->Session->read('Admin.id'));
        } else {
            Configure::write('context.admin_id', 0);
        }
        
        # if we're looking on the page of someone else, get the identity
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        if($username) {
            if(!isset($this->Identity)) {
                App::import('Model', 'Identity');
                $this->Identity = new Identity;
            }
            
            $splitted = $this->Identity->splitUsername($username);
            $username = $splitted['username'];
            
            $this->Identity->contain();
            $data = $this->Identity->findByUsername($username);
            Configure::write('context.identity', $data['Identity']);
        }
        
        if(Configure::read('context.logged_in_identity') && Configure::read('context.identity')) {
            Configure::write('context.is_self', Configure::read('context.logged_in_identity.id') == Configure::read('context.identity.id'));
        }
        
        if(Configure::read('context.logged_in_identity')) {
            $logged_in_identity = Configure::read('context.logged_in_identity');
            Configure::write('context.is_guest', $logged_in_identity['network_id'] == 0 ? true : false);
        }
    }
    
	private function autoLogin() {
        $login_id = $this->Cookie->read('li'); # login id
            
        if($login_id) {
            if(!isset($this->Identity)) {
                App::import('Model', 'Identity');
                $this->Identity = new Identity();
            }
            
            $this->Identity->contain();
            $identity = $this->Identity->findById($login_id);

            if(!$identity) {
                # not found. delete the cookie.
                $this->Cookie->del('li');
            } else {
                $this->Identity->id = $identity['Identity']['id'];
                $this->Identity->saveField('last_login', date('Y-m-d H:i:s'));
                
                $this->Session->write('Identity', $identity['Identity']);
                # refresh auto login cookie
                $this->Cookie->write('li', $login_id, true, '4 weeks');
            }
        }
    }
    
    /**
     * stores the validation errors and the actual data that was
     * submitted to the session. this is needed, because of our
     * widget system and doing redirects in it.
     *
     * you can see how it is used in AdminsController::settings()
     *
     * also see AppController::retrieveFormErrors()
     */
    public function storeFormErrors($modelName, $data, $validationErrors) {
        $formErrors = $this->Session->read('FormErrors');
        if(!$formErrors) {
            $formErrors = array();
        }
        $formErrors[$modelName] = array(
            'data' => $data,
            'validationErrors' => $validationErrors
        );
        $this->Session->write('FormErrors', $formErrors);
    }
    
    /**
     * retrieving form errors for a given model from the session.
     * $this->data is set to the previous data, so we can show
     * the user what was wrong and also retsore the validationErrors,
     * so that the form helpers can display them.
     *
     * you can see how it is used in WidgetsController::form_admin_settings()
     *
     * also see AppController::storeFormErrors()
     */
    public function retrieveFormErrors($modelName) {
        $formErrors = $this->Session->read('FormErrors');
        if(is_array($formErrors)) {
            if(isset($formErrors[$modelName])) {
                $this->data = $formErrors[$modelName]['data'];
                $this->{$modelName}->validationErrors = $formErrors[$modelName]['validationErrors'];
                unset($formErrors[$modelName]);
                $this->Session->write('FormErrors', $formErrors);
            }
        }
    }
    
    private function isSystemUpdatePage() {
    	return (strpos($this->here, '/system/update') !== false) ? true : false;
    }
}