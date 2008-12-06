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
    public $helpers = array('javascript', 'html', 'form');
    public $components = array('menu', 'Cookie');
    
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
     * when NoseRub.use_ssl is used and we're not
     * on a secure page
     */
    public function checkSecure() {
        if(defined('SHELL_DISPATCHER')) {
            return;
        }
        
        if(Configure::read('NoseRub.use_ssl')) {
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
       
    private function auto_login() {
        $li = $this->Cookie->read('li'); # login id
            
        if($li) {
            if(!isset($this->Identity)) {
                App::import('Model', 'Identity');
                $this->Identity = new Identity();
            }
            
            $this->Identity->contain();
            $identity = $this->Identity->findById($li);

            if(!$identity) {
                # not found. delete the cookie.
                $this->Cookie->del('li');
            } else {
                $this->Session->write('Identity', $identity['Identity']);
                # refresh auto login cookie
                $this->Cookie->write('li', $li, true, '4 weeks');
            }
        }
            
    }

    public function beforeFilter() {
        # check for auto-login
        if(!$this->Session->check('Identity.id')) {
            $this->auto_login();
        }        
        
        # Localization
        App::import('Core', 'l10n');
        $this->L10n = new L10n();
        # if language is already set in session, get that
        $language = $this->Session->read('Config.language');
        if(!$language) {
            # if not, get NoseRub default language and save it
            # in the session
            $language = Configure::read('NoseRub.default_language');
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
            if($this->action != '' && strpos($this->action, 'shell_') !== 0) {
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
        
        if(!$this->Identity->checkSecurityToken($session_identity_id, $security_token)) {
            $this->redirect('/pages/security_check/', null, true);
        }
    }
    
    public function beforeRender() {
        if($this->viewPath != 'errors' && strpos($this->here, '/system/update') === false) {
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
}